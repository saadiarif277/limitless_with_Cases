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

        // Check if user has permission to view the case
        if (!$this->canViewCase($user, $case)) {
            abort(403, 'You do not have permission to view this case.');
        }

        // Load case relationships
        $case->load(['patient', 'attorney', 'pilotingPhysician']);

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

        return Inertia::render('panel/user/cases/case-view', [
            'caseDetails' => $caseData,
            'userRole' => $role,
            'userId' => $user->id,
            'referrals' => $referrals,
            'allCptCodes' => $allCptCodes,
        ]);
    }

    /**
     * Check if user has permission to view the case
     */
    private function canViewCase($user, $case): bool
    {
        $role = strtolower($user->roles->first()->name);
        $userId = auth()->id();

        Log::info("role: " . $role);
        Log::info("userId: " . $userId);
        Log::info("case->piloting_physician_id: " . $case->piloting_physician_id);
        Log::info("case->attorney_id: " . $case->attorney_id);

        switch ($role) {
            case 'attorney':
                Log::info("User " . $userId . " is an attorney");
                return $case->attorney_id == $userId;
            case 'case_manager':
                Log::info("User " . $userId . " is a case manager");
                return $case->attorney_id == $userId;
            case 'doctor':
                Log::info("User " . $userId . " is a doctor");
                return $case->piloting_physician_id == $userId;
            case 'patient':
                Log::info("User " . $userId . " is a patient");
                return $case->patient_id == $userId;
            default:
                Log::info("User " . $userId . " is in default case");
                return false;
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

        // Doctors can see referrals they created or are assigned to
        if ($role === 'doctor') {
            $referrals = Referral::whereIn('referral_id', $referralIds)
                               ->where(function($query) use ($userId) {
                                   $query->where('doctor_user_id', $userId)
                                         ->orWhere('source_user_id', $userId);
                               })

                               ->orderBy('created_at', 'desc')
                               ->get()
                               ->toArray();
        }
        // Attorneys and case managers can see all referrals
        elseif (in_array($role, ['attorney', 'case_manager'])) {
            $referrals = Referral::whereIn('referral_id', $referralIds)

                               ->orderBy('created_at', 'desc')
                               ->get()
                               ->toArray();
        }

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

        // Check if user has permission to update billing
        if (!$this->canViewCase($user, $case)) {
            abort(403, 'You do not have permission to update this case.');
        }

        // Validate the request data
        $validated = $request->validate([
            'billing_type' => 'required|in:Insurance,LOP',
            'cpt_codes' => 'required|json',
            'is_cms1500_generated' => 'required|boolean',
        ]);

        // Update the case
        $case->update([
            'billing_type' => $validated['billing_type'],
            'cpt_codes' => $validated['cpt_codes'],
            'is_cms1500_generated' => $validated['is_cms1500_generated'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Billing information updated successfully.'
        ]);
    }
}
