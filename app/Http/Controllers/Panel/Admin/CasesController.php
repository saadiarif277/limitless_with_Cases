<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentCategoryResource;
use App\Http\Resources\MedicalSpecialtyResource;
use App\Http\Resources\ReferralResource;
use App\Http\Resources\CaseResource;
use App\Http\Resources\ReferralReasonResource;
use App\Http\Resources\IcdCodeResource;
use App\Http\Resources\CptCodeResource;
use App\Http\Resources\ReferralStatusResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\DestroyReferralRequest;
use App\Http\Requests\StoreReferralRequest;

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Http;

class CasesController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request): Response
    // {
    //     return Inertia::render('panel/admin/cases/cases-list', [
    //         'cases' => CaseResource::collection(
    //           Cases::query()
    //                 ->select('cases.case_id as case_id', 'patients.name as patient_name', 'attorneys.name as attorney_name','doctors.name as piloting_physician','cases.billing_type as bill_type')
    //                 ->join('users as patients', 'cases.patient_id', '=', 'patients.user_id')
    //                 ->join('users as attorneys', 'cases.attorney_id', '=', 'attorneys.user_id')
    //                 ->join('users as doctors', 'cases.piloting_physician_id', '=', 'doctors.user_id')
    //                 ->get()
    //         ),

    //     ]);
    // }

    public function index(Request $request): Response
    {
        // Fetch all cases with necessary fields
        $cases = Cases::query()
            ->select(
                'cases.case_id as case_id',
                'patients.name as patient_name',
                'attorneys.name as attorney_name',
                'doctors.name as piloting_physician',
                'cases.billing_type as bill_type',
                'cases.is_closed as is_closed',
                'cases.case_won as case_won',
                'cases.reduction_accepted as reduction_accepted'
            )
            ->join('users as patients', 'cases.patient_id', '=', 'patients.user_id')
            ->join('users as attorneys', 'cases.attorney_id', '=', 'attorneys.user_id')
            ->join('users as doctors', 'cases.piloting_physician_id', '=', 'doctors.user_id')
            ->get();

        // Calculate metrics
        $totalCases = $cases->count();
        $pendingCases = $cases->where('status', 'Pending')->count();
        $completeCases = $cases->where('status', 'Complete')->count();
        $reductionRequestSent = $cases->where('reduction_accepted', true)->count();
        $wonCases = $cases->where('case_won', true)->count();
        $lostCases = $cases->where('case_won', false)->where('is_closed', true)->count();

        return Inertia::render('panel/admin/cases/cases-list', [
            'cases' => CaseResource::collection($cases),
            'metrics' => [
                'totalCases' => $totalCases,
                'pendingCases' => $pendingCases,
                'completeCases' => $completeCases,
                'reductionRequestSent' => $reductionRequestSent,
                'wonCases' => $wonCases,
                'lostCases' => $lostCases,
            ],
        ]);
    }
    // public function updateBilling(Request $request, $case_id)
    // {
    //     // Find the case
    //     $case = Cases::findOrFail($case_id);


    //     // Validate the request data
    //     $data = $request->validate([
    //         'billing_type' => 'required|in:LOP,Insurance',
    //         'cpt_codes' => 'nullable|json', // Ensure cpt_codes is a valid JSON string
    //         'is_cms1500_generated' => 'boolean',
    //     ]);



    //     // Decode the cpt_codes JSON string into an array
    //     if (isset($data['cpt_codes'])) {
    //         $data['cpt_codes'] = json_decode($data['cpt_codes'], true);

    //     } else {
    //         $data['cpt_codes'] = []; // Set to an empty array if cpt_codes is null
    //     }

    //     // Update the case with the validated and processed data
    //     $case->update($data);

    //     // Redirect back with a success message
    //     return redirect()->back()->with('success', 'Billing information updated successfully.');
    // }

    public function updateBilling(Request $request, $case_id)
{
    // Find the case
    $case = Cases::findOrFail($case_id);

    // Validate the request data
    $data = $request->validate([
        'billing_type' => 'required|in:LOP,Insurance',
        'cpt_codes' => 'nullable|json', // Ensure cpt_codes is a valid JSON string
        'is_cms1500_generated' => 'boolean',
    ]);

    // Decode the cpt_codes JSON string into an array
    if (isset($data['cpt_codes'])) {
        $data['cpt_codes'] = json_decode($data['cpt_codes'], true);

        // Validate the decoded cpt_codes array
        if (!is_array($data['cpt_codes'])) {
            return redirect()->back()->withErrors(['cpt_codes' => 'Invalid CPT codes format.'])->withInput();
        }
    } else {
        $data['cpt_codes'] = []; // Set to an empty array if cpt_codes is null
    }

    // Update the case with the validated and processed data
    $case->update($data);

    // Redirect back with a success message
    return redirect()->back()->with('success', 'Billing information updated successfully.');
}

    /**
     * Show the form for creating a new resource.
     */
    // public function create(Request $request): Response
    // {
    //     return Inertia::render('panel/admin/cases/cases-create', [
    //         'attorneys' => UserResource::collection(
    //             User::query()
    //                 ->whereHas('roles', function($query) {
    //                     $query->where('name', 'Attorney');
    //                 })
    //                 ->whereHas('lawFirm', function ($query) use ($request) {
    //                     $query->where('law_firms.state_id', $request->get('state_id', 0));
    //                 })
    //                 ->orderBy('name')
    //                 ->get()
    //         ),
    //         'doctors' => UserResource::collection(
    //             User::query()
    //                 ->with('clinics')
    //                 ->whereHas('clinics', function ($query) use ($request) {
    //                     $query->where('clinics.state_id', $request->get('state_id', 0));
    //                 })
    //                 ->whereHas('roles', function($query) {
    //                     $query->where('name', 'Doctor');
    //                 })
    //                 ->orderBy('name')
    //                 ->get()
    //         ),
    //         'documentCategories' => DocumentCategoryResource::collection(
    //             DocumentCategory::query()
    //                 ->with('documentTypes', function ($query) use ($request) {
    //                     $state = State::find($request->get('state_id'));
    //                     $documentTypeIds = $state
    //                         ? $state->documentTypes->pluck('document_type_id')->toArray()
    //                         : [];

    //                     $query
    //                         ->whereIn('document_types.document_type_id', $documentTypeIds)
    //                         ->where('document_types.is_generated', false)
    //                         ->where('document_types.is_permanent', true);
    //                 })
    //                 ->whereHas('documentTypes')
    //                 ->orderBy('name')
    //                 ->get()
    //         ),
    //         'medicalSpecialties' => MedicalSpecialtyResource::collection(
    //             MedicalSpecialty::query()
    //                 ->orderBy('name')
    //                 ->get()
    //         ),
    //         'patients' => UserResource::collection(
    //             User::query()
    //                 ->whereHas('roles', function($query) {
    //                     $query->where('name', 'Patient');
    //                 })
    //                 ->where('state_id', $request->get('state_id', 0))
    //                 ->orderBy('name')
    //                 ->get()
    //         ),
    //         'referralReasons' => ReferralReasonResource::collection(
    //             ReferralReason::query()
    //                 ->orderBy('name')
    //                 ->get()
    //         ),
    //         'referralStatuses' => ReferralStatusResource::collection(
    //             ReferralStatus::query()
    //                 ->orderBy('order', 'asc')
    //                 ->get()
    //         ),
    //         'states' => StateResource::collection(
    //             State::query()
    //                 ->with('documentTypes')
    //                 ->orderBy('name')
    //                 ->get()
    //         ),
    //     ]);
    // }

    public function create(Request $request): Response
    {
        return Inertia::render('panel/admin/cases/cases-create', [
            // Attorneys
            'attorneys' => UserResource::collection(
                User::query()
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'Attorney');
                    })
                    ->orderBy('name')
                    ->get()
            ),

            // Doctors
            'doctors' => UserResource::collection(
                User::query()
                    ->with('clinics')
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'Doctor');
                    })
                    ->orderBy('name')
                    ->get()
            ),

            // Patients
            'patients' => UserResource::collection(
                User::query()
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'Patient');
                    })
                    ->orderBy('name')
                    ->get()
            ),

            // Referrals
            'referrals' => ReferralResource::collection(
                Referral::query()
                    ->with(['patientUser', 'attorneyUser', 'doctorUser'])
                    ->orderBy('referral_date', 'desc')
                    ->get()
            ),

            // ICD Codes
            'icdCodes' => IcdCodeResource::collection(
                IcdCode::query()
                    ->when($request->filled('searchQuery'), function ($query) use ($request) {
                        $searchTerm = strtolower($request->get('searchQuery'));
                        $query->whereRaw('LOWER(code) LIKE ?', ["%{$searchTerm}%"]);
                    })
                    ->orderBy('code')
                    ->paginate(10)
            ),

            // CPT Codes
            'cptCodes' => CptCodeResource::collection(
                CptCode::query()
                    ->when($request->filled('searchQuery'), function ($query) use ($request) {
                        $searchTerm = strtolower($request->get('searchQuery'));
                        $query->whereRaw('LOWER(code) LIKE ?', ["%{$searchTerm}%"]);
                    })
                    ->orderBy('code')
                    ->paginate(10)
            ),

            // Policy Limit Options
            'policyLimitOptions' => [
                ['value' => '50000', 'label' => '$50,000'],
                ['value' => '100000', 'label' => '$100,000'],
                ['value' => '250000', 'label' => '$250,000'],
                ['value' => '500000', 'label' => '$500,000'],
                ['value' => '1000000', 'label' => '$1,000,000'],
            ],

            // Billing Type Options
            'billingTypeOptions' => [
                ['value' => 'Insurance', 'label' => 'Insurance'],
                ['value' => 'LOP', 'label' => 'Letter of Protection (LOP)'],
            ],

            // Document Categories
            'documentCategories' => DocumentCategoryResource::collection(
                DocumentCategory::query()
                    ->with('documentTypes', function ($query) {
                        $query->where('document_types.is_generated', false)
                              ->where('document_types.is_permanent', true);
                    })
                    ->whereHas('documentTypes')
                    ->orderBy('name')
                    ->get()
            ),

            // Medical Specialties
            'medicalSpecialties' => MedicalSpecialtyResource::collection(
                MedicalSpecialty::query()
                    ->orderBy('name')
                    ->get()
            ),

            // Referral Reasons
            'referralReasons' => ReferralReasonResource::collection(
                ReferralReason::query()
                    ->orderBy('name')
                    ->get()
            ),

            // Referral Statuses
            'referralStatuses' => ReferralStatusResource::collection(
                ReferralStatus::query()
                    ->orderBy('order', 'asc')
                    ->get()
            ),

            // States
            'states' => StateResource::collection(
                State::query()
                    ->with('documentTypes')
                    ->orderBy('name')
                    ->get()
            ),
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'referral_status_id' => 'required|exists:referral_statuses,referral_status_id',
            'state_id' => 'required|exists:states,state_id',
            'patient_id' => 'required|exists:users,user_id',
            'doctor_id' => 'required|exists:users,user_id',
            'piloting_physician_id' => 'required|exists:users,user_id',
            'attorney_id' => 'required|exists:users,user_id',
            'policy_limit_info' => 'nullable|string',
            'pip' => 'nullable|boolean',
            'defendant_insurance' => 'nullable|string',
            'plaintiff_insurance' => 'nullable|string',
            'commercial_case' => 'nullable|boolean',
            'type_of_accident' => 'nullable|string',
            'icd_codes' => 'nullable|array', // Validate that icd_codes is an array
            'icd_codes.*' => 'string', // Ensure each ICD code is a string
            'referral_ids' => 'nullable|array', // Validate that referral_ids is an array
            'referral_ids.*' => 'exists:referrals,referral_id', // Ensure each referral ID exists
            'cpt_codes' => 'nullable|string',
            'service_billed' => 'required|numeric',
            'billing_type' => 'nullable|string|in:Insurance,LOP',
            'is_cms1500_generated' => 'required|boolean',
            'case_won' => 'nullable|boolean',
            'outcome' => 'nullable|string',
            'reduction_accepted' => 'nullable|boolean',
            'is_closed' => 'required|boolean',
            'closed_at' => 'nullable|date',
        ]);

        try {
            // Convert arrays to comma-separated strings for database storage
            $validatedData['icd10_codes'] = implode(',', $validatedData['icd_codes'] ?? []);
            $validatedData['referral_ids'] = implode(',', $validatedData['referral_ids'] ?? []);

            // Set primary_referral_id to the first referral ID
            $validatedData['primary_referral_id'] = $validatedData['referral_ids'][0] ?? null;

            // Create the case
            $case = Cases::create([
                'referral_status_id' => $validatedData['referral_status_id'],
                'state_id' => $validatedData['state_id'],
                'patient_id' => $validatedData['patient_id'],
                'doctor_id' => $validatedData['doctor_id'],
                'piloting_physician_id' => $validatedData['piloting_physician_id'],
                'attorney_id' => $validatedData['attorney_id'],
                'policy_limit_info' => $validatedData['policy_limit_info'],
                'pip' => $validatedData['pip'],
                'defendant_insurance' => $validatedData['defendant_insurance'],
                'plaintiff_insurance' => $validatedData['plaintiff_insurance'],
                'commercial_case' => $validatedData['commercial_case'],
                'type_of_accident' => $validatedData['type_of_accident'],
                'primary_referral_id' => $validatedData['primary_referral_id'],
                'icd10_codes' => $validatedData['icd10_codes'],
                'referral_ids' => $validatedData['referral_ids'],
                'cpt_codes' => $validatedData['cpt_codes'],
                'service_billed' => $validatedData['service_billed'],
                'billing_type' => $validatedData['billing_type'],
                'is_cms1500_generated' => $validatedData['is_cms1500_generated'],
                'case_won' => $validatedData['case_won'],
                'outcome' => $validatedData['outcome'],
                'reduction_accepted' => $validatedData['reduction_accepted'],
                'is_closed' => $validatedData['is_closed'],
                'closed_at' => $validatedData['closed_at'],
            ]);

            // Redirect back with success message
            return redirect()->back()->with('success', 'Case created successfully!');
        } catch (\Exception $e) {
            // Redirect back with error message
            return redirect()->back()->with('error', 'Failed to create case. Please try again.');
        }
    }


    /**
     * Display the specified resource.
     */
    // public function show($caseId)
    // {
    //     // Fetch the case details by case_id
    //     $case = Cases::with(['patient', 'attorney']) // Assuming you have relationships defined
    //                 ->where('case_id', $caseId)
    //                 ->firstOrFail();
    //     $case->policy_limit_info = json_decode($case->policy_limit_info);
    //     // Fetch the referrals related to the case
    //     $referrals = Referral::where('referral_id', $case->primary_referral_id) // Assuming `case_id` is the relation key
    //                         ->get();
    //     $referrals = ReferralResource::collection($referrals);
    //     // Return the data to the Inertia view
    //     return Inertia::render('panel/admin/cases/case-view', [
    //         'caseDetails' => $case,
    //         'referrals' => $referrals,
    //     ]);
    // }
    // public function show($caseId)
    // {
    //     // Fetch the case details by case_id
    //     $case = Cases::with(['patient', 'attorney']) // Assuming you have relationships defined
    //                 ->where('case_id', $caseId)
    //                 ->firstOrFail();

    //     // Decode the policy limit info from JSON (if stored as JSON)
    //     $case->policy_limit_info = json_decode($case->policy_limit_info);

    //     // Fetch all referrals related to the case using referral_ids
    //     $referralIds = explode(',', $case->referral_ids); // Split the comma-separated referral IDs
    //     $referrals = Referral::whereIn('referral_id', $referralIds) // Fetch all referrals
    //                         ->with(['reductionRequests']) // Eager load reduction requests
    //                         ->get();

    //     // Process each referral to include reduction request details
    //     $referrals->each(function ($referral) {
    //         $referral->reductionRequests->each(function ($reductionRequest) {
    //             // Add a badge for status
    //             $reductionRequest->status_badge = $this->getStatusBadge($reductionRequest->referral_status);
    //             // Format the amount
    //             $reductionRequest->formatted_amount = number_format($reductionRequest->amount, 2);
    //             // Add a link to the file if it exists
    //             if ($reductionRequest->file_path) {
    //                 $reductionRequest->file_link = asset('storage/' . $reductionRequest->file_path);
    //             } else {
    //                 $reductionRequest->file_link = null;
    //             }
    //         });
    //     });

    //     // Fetch ICD-10 code descriptions (handling comma-separated values)
    //     $icdCodeDescriptions = []; // Default fallback
    //     if ($case->icd10_codes) {
    //         $icdCodeValues = explode(',', $case->icd10_codes); // Split the comma-separated ICD-10 values

    //         // Check if the values are IDs or codes
    //         if (is_numeric($icdCodeValues[0])) {
    //             // If the first value is numeric, assume they are IDs
    //             $icdCodeDescriptions = IcdCode::whereIn('id', $icdCodeValues) // Fetch by IDs
    //                                         ->pluck('description')
    //                                         ->toArray();
    //         } else {
    //             // If the first value is not numeric, assume they are codes
    //             $icdCodeDescriptions = IcdCode::whereIn('code', $icdCodeValues) // Fetch by codes
    //                                         ->pluck('description')
    //                                         ->toArray();
    //         }

    //         // If no descriptions are found, set a fallback message
    //         if (empty($icdCodeDescriptions)) {
    //             $icdCodeDescriptions = ['No ICD-10 codes'];
    //         }
    //     } else {
    //         $icdCodeDescriptions = ['No ICD-10 codes']; // Fallback if icd10_codes is null or empty
    //     }

    //     // Fetch CPT codes descriptions (splitting the comma-separated values)
    //     $cptCodeDescriptions = []; // Default fallback
    //     if ($case->cpt_codes) {
    //         $cptCodeIds = explode(',', $case->cpt_codes); // Split the comma-separated CPT code IDs
    //         $cptCodeDescriptions = CptCode::whereIn('id', $cptCodeIds) // Fetch descriptions
    //                                     ->pluck('description')
    //                                     ->toArray();

    //         // If no descriptions are found, set a fallback message
    //         if (empty($cptCodeDescriptions)) {
    //             $cptCodeDescriptions = ['No CPT codes'];
    //         }
    //     } else {
    //         $cptCodeDescriptions = ['No CPT codes']; // Fallback if cpt_codes is null or empty
    //     }

    //     // Return the data to the Inertia view
    //     return Inertia::render('panel/admin/cases/case-view', [
    //         'caseDetails' => $case,
    //         'icdCodeDescriptions' => $icdCodeDescriptions, // Updated to handle multiple ICD-10 codes
    //         'cptCodeDescriptions' => $cptCodeDescriptions,
    //         'referrals' => $referrals,
    //     ]);
    // }

    public function show($caseId)
{
    // Fetch the case details by case_id
    $case = Cases::with(['patient', 'attorney']) // Assuming you have relationships defined
                ->where('case_id', $caseId)
                ->firstOrFail();

    // Decode the policy limit info from JSON (if stored as JSON)
    $case->policy_limit_info = json_decode($case->policy_limit_info);

    // Fetch all referrals related to the case using referral_ids
    $referralIds = explode(',', $case->referral_ids); // Split the comma-separated referral IDs
    $referrals = Referral::whereIn('referral_id', $referralIds) // Fetch all referrals
                        ->with(['reductionRequests']) // Eager load reduction requests
                        ->get();

    // Process each referral to include reduction request details
    $referrals->each(function ($referral) {
        $referral->reductionRequests->each(function ($reductionRequest) {
            // Add a badge for status
            $reductionRequest->status_badge = $this->getStatusBadge($reductionRequest->referral_status);
            // Format the amount
            $reductionRequest->formatted_amount = number_format($reductionRequest->amount, 2);
            // Add a link to the file if it exists
            if ($reductionRequest->file_path) {
                $reductionRequest->file_link = asset('storage/' . $reductionRequest->file_path);
            } else {
                $reductionRequest->file_link = null;
            }
        });
    });

    // Fetch ICD-10 code descriptions (handling comma-separated values)
    $icdCodeDescriptions = []; // Default fallback
    if ($case->icd10_codes) {
        $icdCodeValues = explode(',', $case->icd10_codes); // Split the comma-separated ICD-10 values

        // Check if the values are IDs or codes
        if (is_numeric($icdCodeValues[0])) {
            // If the first value is numeric, assume they are IDs
            $icdCodeDescriptions = IcdCode::whereIn('id', $icdCodeValues) // Fetch by IDs
                                        ->pluck('description')
                                        ->toArray();
        } else {
            // If the first value is not numeric, assume they are codes
            $icdCodeDescriptions = IcdCode::whereIn('code', $icdCodeValues) // Fetch by codes
                                        ->pluck('description')
                                        ->toArray();
        }

        // If no descriptions are found, set a fallback message
        if (empty($icdCodeDescriptions)) {
            $icdCodeDescriptions = ['No ICD-10 codes'];
        }
    } else {
        $icdCodeDescriptions = ['No ICD-10 codes']; // Fallback if icd10_codes is null or empty
    }

    // Fetch all available CPT codes for the dropdown
    $allCptCodes = CptCode::all(['id', 'code', 'description']); // Fetch all CPT codes with their descriptions

    // Fetch CPT codes descriptions for the case (splitting the comma-separated values)
    $cptCodeDescriptions = []; // Default fallback
    if ($case->cpt_codes) {
        $cptCodeIds = explode(',', $case->cpt_codes); // Split the comma-separated CPT code IDs
        $cptCodeDescriptions = CptCode::whereIn('id', $cptCodeIds) // Fetch descriptions
                                    ->pluck('description')
                                    ->toArray();

        // If no descriptions are found, set a fallback message
        if (empty($cptCodeDescriptions)) {
            $cptCodeDescriptions = ['No CPT codes'];
        }
    } else {
        $cptCodeDescriptions = ['No CPT codes']; // Fallback if cpt_codes is null or empty
    }

    // Return the data to the Inertia view
    return Inertia::render('panel/admin/cases/case-view', [
        'caseDetails' => $case,
        'icdCodeDescriptions' => $icdCodeDescriptions, // Updated to handle multiple ICD-10 codes
        'cptCodeDescriptions' => $cptCodeDescriptions,
        'referrals' => $referrals,
        'allCptCodes' => $allCptCodes, // Pass all CPT codes for the dropdown
    ]);
}
    // Helper function to get status badge
    private function getStatusBadge($status)
    {
        switch ($status) {
            case 'pending':
                return '<span class="badge badge-warning">Pending</span>';
            case 'accepted':
                return '<span class="badge badge-success">Accepted</span>';
            case 'rejected':
                return '<span class="badge badge-danger">Rejected</span>';
            default:
                return '<span class="badge badge-secondary">Unknown</span>';
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
     * Save the CMS 1500 form data
     */
    public function saveForm(Request $request, Cases $case)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'form_data' => 'required|array',
                'status' => 'required|in:draft,completed'
            ]);

            // Create a new form record
            $form = $case->forms()->create([
                'form_data' => $validated['form_data'],
                'status' => $validated['status'],
                'type' => 'cms1500'
            ]);

            // If the form is completed, generate and store the PDF
            if ($validated['status'] === 'completed') {
                // Generate PDF using DocuSeal API
                $pdf = $this->generateFormPDF($validated['form_data']);

                // Store the PDF
                $form->update([
                    'pdf_path' => $pdf
                ]);
            }

            return response()->json([
                'success' => true,
                'form_id' => $form->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download the CMS 1500 form PDF
     */
    public function downloadForm(Cases $case, Form $form)
    {
        try {
            // Check if the form belongs to the case
            if ($form->case_id !== $case->id) {
                abort(404);
            }

            // Check if the form has a PDF
            if (!$form->pdf_path) {
                abort(404);
            }

            // Get the PDF file path
            $path = storage_path('app/' . $form->pdf_path);

            // Check if the file exists
            if (!file_exists($path)) {
                abort(404);
            }

            // Return the PDF file
            return response()->file($path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="CMS-1500-Form.pdf"'
            ]);
        } catch (\Exception $e) {
            abort(500);
        }
    }

    /**
     * Generate PDF from form data using DocuSeal API
     */
    private function generateFormPDF($formData)
    {
        try {
            // Make API call to DocuSeal to generate PDF
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.docuseal.api_key'),
                'Content-Type' => 'application/json'
            ])->post('https://api.docuseal.com/v1/submissions/generate_pdf', [
                'form_data' => $formData
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to generate PDF');
            }

            // Get the PDF content
            $pdfContent = $response->body();

            // Generate a unique filename
            $filename = 'cms1500_' . uniqid() . '.pdf';

            // Store the PDF
            Storage::put('public/forms/' . $filename, $pdfContent);

            // Return the relative path
            return 'public/forms/' . $filename;
        } catch (\Exception $e) {
            throw new \Exception('Failed to generate PDF: ' . $e->getMessage());
        }
    }
}
