<?php

namespace App\Http\Controllers\Panel\User;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentCategoryResource;
use App\Http\Resources\MedicalSpecialtyResource;
use App\Http\Resources\ReferralResource;
use App\Http\Resources\CaseResource;
use App\Http\Resources\ReferralReasonResource;
use App\Http\Resources\ReferralStatusResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\DestroyReferralRequest;
use App\Http\Requests\StoreReferralRequest;
use App\Http\Requests\UpdateReferralRequest;
use App\Repositories\ReferralRepository;
use App\Models\Document;
use App\Models\Cases;
use App\Models\IcdCode;
use App\Models\CptCode;
use App\Models\DocumentCategory;
use App\Models\DocumentType;
use App\Models\MedicalSpecialty;
use App\Models\Referral;
use App\Models\ReferralReason;
use App\Models\ReferralStatus;
use App\Models\State;
use App\Models\User;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Attorney;
use App\Models\Doctor;
use App\Models\Patient;
use App\Http\Resources\CptCodeResource;

class CasesController extends Controller
{
    private ReferralRepository $referralRepository;

    /**
     * Create a new instance.
     */
    public function __construct(ReferralRepository $referralRepository)
    {
        $this->referralRepository = $referralRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
{
        $user = $request->user();
        $query = Cases::query();
        $userRole = $user->roles->first()->name;

        // Filter cases based on user role
        switch ($userRole) {
            case 'attorney':
            case 'case_manager':
                $query->where('attorney_id', $user->id);
                break;
            case 'doctor':
                $query->where(function ($q) use ($user) {
                    $q->where('piloting_physician_id', $user->id);
                });
                break;
            case 'patient':
                $query->where('patient_id', $user->id);
                break;
        }

        // Load necessary relationships with correct primary keys
        $cases = $query->with([
            'patient:user_id,name,email',
            'attorney:user_id,name,email',
            'pilotingPhysician:user_id,name,email',
            'primaryReferral'
        ])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($case) {
            // Format the case data
            $caseData = $case->toArray();
            $caseData['policy_limit_info'] = json_decode($case->policy_limit_info, true);

            // Add status information
            $caseData['status'] = $case->status;

            // Add piloting physician information
            $caseData['piloting_physician_info'] = [
                'name' => $case->pilotingPhysician ? $case->pilotingPhysician->name : null,
                'email' => $case->pilotingPhysician ? $case->pilotingPhysician->email : null,
            ];

            return $caseData;
        });

        // Determine if user can create cases
        $canCreateCase = in_array($userRole, ['Attorney', 'Case_manager', 'Doctor']);

    return Inertia::render('panel/user/cases/cases-list', [
            'cases' => $cases,
            'userRole' => $userRole,
            'userId' => $user->id,
            'canCreateCase' => $canCreateCase,
    ]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $user = Auth::user();
            $userRole = $user->roles->first()->name;
            $stateId = $user->state_id;

            // Get states
            $states = StateResource::collection(
                State::query()
                    ->orderBy('name')
                    ->get()
            );

            // Get CPT codes
            $allCptCodes = CptCodeResource::collection(
                CptCode::query()
                    ->orderBy('code')
                    ->get()
            );



            // Get referrals based on user role
            $referrals = ReferralResource::collection(
                Referral::query()
                    ->with(['patientUser'])
                    ->when($userRole === 'Attorney', function ($query) use ($stateId) {
                        return $query->where('attorney_user_id', auth()->id())
                            ->where('state_id', $stateId);
                    })
                    ->when($userRole === 'Doctor', function ($query) use ($stateId) {
                        return $query->where('doctor_user_id', auth()->id())
                            ->where('state_id', $stateId);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get()
            );

            // Get data based on user role
            if ($userRole === 'Attorney') {
                $doctors = UserResource::collection(
                    User::query()
                        ->whereHas('roles', function($query) {
                            $query->where('name', 'Doctor');
                        })
                        ->Orwhere('law_firm_id', Auth::user()->law_firm_id)
                        ->Orwhere('state_id', Auth::user()->state_id)
                        ->orderBy('name')
                        ->get()
                );

                $patients = UserResource::collection(
                    User::query()
                        ->whereHas('roles', function($query) {
                            $query->where('name', 'Patient');
                        })
                        ->where('state_id', $stateId)
                        ->orderBy('name')
                        ->get()
                );
            } elseif ($userRole === 'Doctor') {
                $attorneys = UserResource::collection(
                    User::query()
                        ->whereHas('roles', function($query) {
                            $query->where('name', 'Attorney');
                        })
                        ->where('state_id', $stateId)
                        ->orderBy('name')
                        ->get()
                );

                $patients = UserResource::collection(
                    User::query()
                        ->whereHas('roles', function($query) {
                            $query->where('name', 'Patient');
                        })
                        ->where('state_id', $stateId)
                        ->orderBy('name')
                        ->get()
                );
            }

            return Inertia::render('panel/user/cases/create-case', [
                'attorneys' => $attorneys ?? [],
                'doctors' => $doctors ?? [],
                'patients' => $patients,
                'states' => $states,
                'allCptCodes' => $allCptCodes,
                'referrals' => $referrals,
                'userRole' => $userRole,
                'userId' => $user->id,
                'listRoute' => 'panel.user.cases.index',
                'storeRoute' => 'panel.user.cases.store',
                'error' => null
            ]);
        } catch (\Exception $e) {
            Log::error('Error in create method: ' . $e->getMessage());
            return Inertia::render('panel/user/cases/create-case', [
                'error' => 'An error occurred while loading the form.',
                'attorneys' => [],
                'doctors' => [],
                'patients' => [],
                'states' => [],
                'allCptCodes' => [],
                'referrals' => [],
                'userRole' => $userRole ?? '',
                'userId' => $user->id ?? 0,
                'listRoute' => 'panel.user.cases.index',
                'storeRoute' => 'panel.user.cases.store'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $userRole = $user->roles->first()->name;

            // Base validation rules for all roles
            $baseRules = [
                'state_id' => 'required|exists:states,state_id',
                'patient_id' => 'required|exists:users,user_id',
                'type_of_accident' => 'required|string',
                'primary_referral_id' => 'nullable|exists:referrals,referral_id',
                'referral_ids' => 'nullable|array',
                'referral_ids.*' => 'exists:referrals,referral_id',
            ];

            // Role-specific validation rules
            $roleRules = [];
            if ($userRole === 'Attorney') {
                $roleRules = [
                    'piloting_physician_id' => 'required|exists:users,user_id',
                    'policy_limit' => 'required|numeric|min:0',
                    'pip_coverage' => 'required|numeric|min:0',
                    'commercial_case' => 'required|boolean',
                    'reduction_amount' => 'nullable|numeric|min:0',
                    'reduction_notes' => 'nullable|string|max:1000',
                ];
            } else if ($userRole === 'Doctor') {
                $roleRules = [
                    'attorney_id' => 'required|exists:users,user_id',
                    'billing_type' => 'required|in:Insurance,LOP',
                    'service_billed' => 'required|numeric|min:0',
                    'cpt_codes' => 'nullable',
                    'cpt_codes.*.code' => 'required_with:cpt_codes|string',
                    'cpt_codes.*.value' => 'required_with:cpt_codes|numeric|min:0',
                ];
            }

            // Merge the base and role-specific validation rules
            $validator = Validator::make($request->all(), array_merge($baseRules, $roleRules));

            if ($validator->fails()) {
                return redirect()
                    ->route('panel.user.cases.create')
                    ->withErrors($validator)
                    ->withInput();
            }

            $validatedData = $validator->validated();

            // Set the appropriate IDs based on user role
            if ($userRole === 'Attorney') {
                $validatedData['attorney_id'] = $user->user_id;
                // Encode policy limit info into JSON
                $validatedData['policy_limit_info'] = json_encode([
                    'policy_limit' => $request->input('policy_limit'),
                    'pip_coverage' => $request->input('pip_coverage'),
                    'commercial_case' => $request->input('commercial_case', false)
                ]);

                // Handle reduction fields
                if ($request->filled('reduction_amount')) {
                    $validatedData['reduction_amount'] = $request->input('reduction_amount');
                    $validatedData['reduction_requested'] = true;
                }
                if ($request->filled('reduction_notes')) {
                    $validatedData['attorney_notes'] = $request->input('reduction_notes');
                }
            } else if ($userRole === 'Doctor') {
                $validatedData['piloting_physician_id'] = $user->user_id;

                // Handle CPT codes
                if ($request->has('cpt_codes') && is_array($request->input('cpt_codes'))) {
                    // Filter out any empty CPT codes
                    $filteredCptCodes = array_filter($request->input('cpt_codes'), function($cpt) {
                        return !empty($cpt['code']) && !empty($cpt['value']);
                    });

                    if (!empty($filteredCptCodes)) {
                        $validatedData['cpt_codes'] = json_encode($filteredCptCodes);
                    }
                }
            }

            // Convert referral_ids array to comma-separated string
            if (isset($validatedData['referral_ids']) && is_array($validatedData['referral_ids'])) {
                // Ensure the first referral is set as primary
                if (!empty($validatedData['referral_ids'])) {
                    $validatedData['primary_referral_id'] = $validatedData['referral_ids'][0];
                }

                // Convert to comma-separated string
                $validatedData['referral_ids'] = implode(',', $validatedData['referral_ids']);
            } else {
                $validatedData['referral_ids'] = null;
            }

            // Set default values for optional fields
            $validatedData['is_closed'] = $request->input('is_closed', false);
            $validatedData['case_won'] = $request->input('case_won', false);
            $validatedData['reduction_accepted'] = $request->input('reduction_accepted', false);

            // Create the case
            $case = Cases::create($validatedData);

            // Create reduction requests if reduction amount is specified
            if ($userRole === 'Attorney' && $request->filled('reduction_amount')) {
                $case->createReductionRequests();
            }

            return redirect()
                ->route('panel.user.cases.create')
                ->with('success', 'Case created successfully!');

        } catch (\Exception $e) {
            Log::error('Error creating case: ' . $e->getMessage());

            return redirect()
                ->route('panel.user.cases.create')
                ->with('error', 'Failed to create case. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Cases $case): Response
    {
        $user = $request->user();
        $role = $user->roles->first()->name;

        // Load case relationships
        $case->load(['patient', 'attorney', 'pilotingPhysician', 'reductionRequests']);

        // Get referrals based on user role
        $referrals = $this->getReferrals($user, $case);

        // Get all CPT codes
        $allCptCodes = CptCode::orderBy('code')->get();

        // Format the case data
        $caseData = $case->toArray();
        $caseData['policy_limit_info'] = json_decode($case->policy_limit_info, true);

        // Process referrals to include reduction request details
        if (!empty($referrals)) {
            foreach ($referrals as &$referral) {
                if (isset($referral['reduction_requests']) && !empty($referral['reduction_requests'])) {
                    foreach ($referral['reduction_requests'] as &$reductionRequest) {
                        if (isset($reductionRequest['file_path']) && $reductionRequest['file_path']) {
                            $reductionRequest['file_link'] = asset('storage/' . $reductionRequest['file_path']);
                        }
                    }
                }
            }
        }

        // Get reduction requests for the case
        $reductionRequests = $case->reductionRequests()->with(['referral.patientUser', 'referral.attorneyUser'])->get();

        return Inertia::render('panel/user/cases/case-view', [
            'caseDetails' => $caseData,
            'userRole' => $role,
            'userId' => $user->id,
            'referrals' => $referrals,
            'allCptCodes' => $allCptCodes,
            'reductionRequests' => $reductionRequests,
        ]);
    }

    /**
     * Check if user has permission to view the case
     */
    private function canViewCase($user, $case): bool
    {
        $role = strtolower($user->roles->first()->name);
        $userId = auth()->id();

        switch ($role) {
            case 'attorney':
            case 'case_manager':
                // Attorneys and case managers can view cases they own
                return $case->attorney_id == $userId;
            case 'doctor':
                // Doctors can view cases where they are the piloting physician
                return $case->piloting_physician_id == $userId;
            case 'patient':
                // Patients can view their own cases
                return $case->patient_id == $userId;
            case 'administrator':
                // Administrators can view all cases
                return true;
            default:
                // For other roles, check if they have any relationship to the case
                return $case->attorney_id == $userId ||
                       $case->piloting_physician_id == $userId ||
                       $case->patient_id == $userId;
        }
    }

    /**
     * Get medical records based on user role and permissions
     */
    private function getMedicalRecords($user, $case): array
    {
        $role = $user->roles->first()->name;
        $records = [];

        // Doctors can see records they created or are public
        if ($role === 'doctor') {
            $records = MedicalRecord::where('case_id', $case->case_id)
                                  ->where(function($query) use ($user) {
                                      $query->where('piloting_physician_id', $user->id)
                                            ->orWhere('is_public', true);
                                  })
                                  ->orderBy('created_at', 'desc')
                                  ->get()
                                  ->toArray();
        }
        // Attorneys and case managers can see basic medical records
        elseif (in_array($role, ['attorney', 'case_manager'])) {
            $records = MedicalRecord::where('case_id', $case->case_id)
                                  ->where('is_public', true)
                                  ->orderBy('created_at', 'desc')
                                  ->get()
                                  ->toArray();
        }

        return $records;
    }

    /**
     * Get referrals based on user role and permissions
     */
    private function getReferrals($user, $case): array
    {
        $role = strtolower($user->roles->first()->name);
        $userId = auth()->id();
        $referrals = [];

        // Get all referral IDs (primary + additional)
        $referralIds = array_filter(array_merge(
            [$case->primary_referral_id],
            $case->referral_ids ? explode(',', $case->referral_ids) : []
        ));

        Log::info("Referral IDs for case: " . json_encode($referralIds));

        // All users can see all referrals for the case
        $referrals = Referral::whereIn('referral_id', $referralIds)
                           ->with(['reductionRequests', 'patientUser', 'attorneyUser', 'doctorUser'])
                           ->orderBy('created_at', 'desc')
                           ->get()
                           ->toArray();

        Log::info("Found referrals: " . json_encode($referrals));
        return $referrals;
    }

    /**
     * Create a new referral
     */
    public function createReferral(Request $request, Cases $case)
    {
        $user = $request->user();

        // Check if user has permission to create referrals
        if ($user->roles->first()->name !== 'doctor') {
            abort(403);
        }

        $validated = $request->validate([
            'specialist_id' => 'required|exists:users,id',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $referral = Referral::create([
            'case_id' => $case->id,
            'specialist_id' => $validated['specialist_id'],
            'reason' => $validated['reason'],
            'notes' => $validated['notes'],
            'created_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'referral' => $referral
        ]);
    }

    /**
     * View a medical record
     */
    public function viewRecord(Request $request, Cases $case, MedicalRecord $record)
    {
        $user = $request->user();

        // Check if user has permission to view this record
        if (!$this->canViewRecord($user, $record)) {
            abort(403);
        }

        return response()->file(storage_path('app/' . $record->file_path));
    }

    /**
     * Check if user has permission to view a medical record
     */
    private function canViewRecord($user, $record): bool
    {
        $role = $user->roles->first()->name;

        switch ($role) {
            case 'piloting_physician':
                return true;
            case 'doctor':
                return $record->doctor_id === $user->id || $record->is_public;
            case 'attorney':
            case 'case_manager':
                return $record->is_public;
            default:
                return false;
        }
    }

    /**
     * Create a reduction request for a referral
     */
    public function createReductionRequest(Request $request, Cases $case)
    {
        $user = $request->user();
        $role = $user->roles->first()->name;

        // Only attorneys and case managers can create reduction requests
        if (!in_array($role, ['Attorney', 'Case_manager'])) {
            abort(403, 'Only attorneys and case managers can create reduction requests.');
        }

        $validated = $request->validate([
            'referral_id' => 'required|exists:referrals,referral_id',
            'amount' => 'required|numeric|min:0',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Store the uploaded file
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('reduction_files', 'public');
        }

        // Create the reduction request
        $reductionRequest = \App\Models\ReductionRequest::create([
            'case_id' => $case->case_id,
            'referral_id' => $validated['referral_id'],
            'amount' => $validated['amount'],
            'file_path' => $filePath,
            'referral_status' => 'reduction_request_sent',
            'doctor_decision' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update referral status
        $referral = \App\Models\Referral::find($validated['referral_id']);
        $referral->update(['referral_status_id' => 5]); // Assuming 5 is "Reduction Request Sent"

        return response()->json([
            'message' => 'Reduction request created successfully',
            'data' => $reductionRequest,
        ], 201);
    }

    /**
     * Update doctor's decision on a reduction request
     */
    public function updateReductionDecision(Request $request, Cases $case, $reductionRequestId)
    {
        $user = $request->user();
        $role = $user->roles->first()->name;

        // Debug logging
        \Log::info('User attempting to update reduction decision', [
            'user_id' => $user->id,
            'user_role' => $role,
            'case_id' => $case->case_id,
            'reduction_request_id' => $reductionRequestId
        ]);

        // Only doctors can update reduction decisions
        if ($role !== 'Doctor') {
            abort(403, 'Only doctors can update reduction decisions. Current role: ' . $role);
        }

        $reductionRequest = \App\Models\ReductionRequest::with(['referral'])->findOrFail($reductionRequestId);

        // Debug logging for reduction request
        \Log::info('Reduction request details', [
            'reduction_request' => $reductionRequest->toArray(),
            'referral_doctor_id' => $reductionRequest->referral->doctor_user_id ?? 'null',
            'user_id' => $user->id
        ]);

        // Verify the doctor owns this referral
        if ($reductionRequest->referral->doctor_user_id != $user->id) {
            abort(403, 'You can only respond to reduction requests for your own referrals. Referral doctor ID: ' . $reductionRequest->referral->doctor_user_id . ', Your ID: ' . $user->id);
        }

        $validated = $request->validate([
            'doctor_decision' => 'required|in:accepted,rejected',
            'counter_offer' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $reductionRequest->update([
            'doctor_decision' => $validated['doctor_decision'],
            'counter_offer' => $validated['counter_offer'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update referral status based on decision
        $referral = $reductionRequest->referral;
        if ($validated['doctor_decision'] === 'accepted') {
            $referral->update(['referral_status_id' => 6]); // Assuming 6 is "Reduction Accepted"
        } elseif ($validated['doctor_decision'] === 'rejected') {
            $referral->update(['referral_status_id' => 7]); // Assuming 7 is "Reduction Rejected"
        }

        return response()->json([
            'message' => 'Reduction decision updated successfully',
            'data' => $reductionRequest->fresh(),
        ]);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReferralRequest $request, Referral $referral)
    {
        $referral->load([
            'patientUser',
        ]);

        $referral->update($request->safe()->only([
            'referral_date',
            'referral_status_id',
        ]));

        if ($request->has('documents') && !empty($request->validated()['documents'])) {
            foreach ($request->validated()['documents'] as $documentTypeId => $document) {
                if (!method_exists($document, 'getClientOriginalExtension')) {
                    continue;
                }

                $documentType = DocumentType::findOrFail($documentTypeId);

                // Generate a file name for the PDF
                $fileName = "";
                $fileName .= Str::slug($referral->patientUser->name);
                $fileName .= '-'.Str::slug($documentType->name);
                $fileName .= '-'.now()->format('Y-m-d-H:i:s');
                $fileName .= '-REF#'.$referral->getKey();
                $fileName .= ".{$document->getClientOriginalExtension()}";

                // Upload the file to the specified path and get the stored file path
                $path = $document->storeAs('public/uploads/documents', $fileName);

                $currentDocument = $referral
                    ->documents()
                    ->where('document_type_id', $documentTypeId)
                    ->first();

                if (!$currentDocument) {
                    $referral->documents()->create([
                        'document_type_id' => $documentTypeId,
                        'name' => $fileName,
                        'path' => $path,
                    ]);
                } else {
                    $currentDocument->update([
                        'name' => $fileName,
                        'path' => $path,
                    ]);
                }

                if ($documentTypeId == DocumentType::MEDICAL_RESULTS) {
                    \App\Jobs\Referral\GenerateInvoiceDocument::dispatchSync($referral);
                }
            }
        }

        return to_route('panel.admin.referrals.show', [
            'referral' => $referral->getKey(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyReferralRequest $request, Referral $referral)
    {
        $referral->documents()->delete();
        $referral->delete();
        return to_route('panel.admin.referrals.index');
    }

    /**
     * Update billing information for a case
     */
    public function updateBilling(Request $request, Cases $case)
    {
        $user = $request->user();
        $role = $user->roles->first()->name;

        // Only doctors can update billing information
        if ($role !== 'Doctor') {
            abort(403, 'Only doctors can update billing information.');
        }

        // Validate the request data
        $validated = $request->validate([
            'billing_type' => 'nullable|in:Insurance,LOP',
            'cpt_codes' => 'nullable|string',
        ]);

        // Update the case
        $case->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Billing information updated successfully.'
        ]);
    }
}
