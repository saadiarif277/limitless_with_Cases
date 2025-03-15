<?php

namespace App\Http\Controllers\Panel\Admin;

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

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

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('panel/admin/cases/cases-create', [
            'attorneys' => UserResource::collection(
                User::query()
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'Attorney');
                    })
                    ->whereHas('lawFirm', function ($query) use ($request) {
                        $query->where('law_firms.state_id', $request->get('state_id', 0));
                    })
                    ->orderBy('name')
                    ->get()
            ),
            'doctors' => UserResource::collection(
                User::query()
                    ->with('clinics')
                    ->whereHas('clinics', function ($query) use ($request) {
                        $query->where('clinics.state_id', $request->get('state_id', 0));
                    })
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'Doctor');
                    })
                    ->orderBy('name')
                    ->get()
            ),
            'documentCategories' => DocumentCategoryResource::collection(
                DocumentCategory::query()
                    ->with('documentTypes', function ($query) use ($request) {
                        $state = State::find($request->get('state_id'));
                        $documentTypeIds = $state
                            ? $state->documentTypes->pluck('document_type_id')->toArray()
                            : [];

                        $query
                            ->whereIn('document_types.document_type_id', $documentTypeIds)
                            ->where('document_types.is_generated', false)
                            ->where('document_types.is_permanent', true);
                    })
                    ->whereHas('documentTypes')
                    ->orderBy('name')
                    ->get()
            ),
            'medicalSpecialties' => MedicalSpecialtyResource::collection(
                MedicalSpecialty::query()
                    ->orderBy('name')
                    ->get()
            ),
            'patients' => UserResource::collection(
                User::query()
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'Patient');
                    })
                    ->where('state_id', $request->get('state_id', 0))
                    ->orderBy('name')
                    ->get()
            ),
            'referralReasons' => ReferralReasonResource::collection(
                ReferralReason::query()
                    ->orderBy('name')
                    ->get()
            ),
            'referralStatuses' => ReferralStatusResource::collection(
                ReferralStatus::query()
                    ->orderBy('order', 'asc')
                    ->get()
            ),
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
    $request->validate([
        'patient_id' => 'required|exists:users,id',
        'attorney_id' => 'nullable|exists:users,id',
        'piloting_physician_id' => 'nullable|exists:users,id',
        'policy_limit_info' => 'nullable|string',
        'primary_referral_id' => 'required|exists:referrals,referral_id',
        'referral_ids' => 'nullable|array', // Validate that referral_ids is an array
        'referral_ids.*' => 'exists:referrals,referral_id', // Ensure each referral ID exists
        'icd10_codes' => 'nullable|string',
        'cpt_codes' => 'nullable|string',
        'service_billed' => 'required|numeric',
        'billing_type' => 'nullable|string',
        'is_cms1500_generated' => 'required|boolean',
        'case_won' => 'nullable|boolean',
        'outcome' => 'nullable|string',
        'reduction_accepted' => 'nullable|boolean',
        'is_closed' => 'required|boolean',
        'closed_at' => 'nullable|date',
    ]);

    // Create the case
    $case = Cases::create([
        'patient_id' => $request->patient_id,
        'attorney_id' => $request->attorney_id,
        'piloting_physician_id' => $request->piloting_physician_id,
        'policy_limit_info' => $request->policy_limit_info,
        'primary_referral_id' => $request->primary_referral_id,
        'referral_ids' => $request->referral_ids ? implode(',', $request->referral_ids) : null, // Store as comma-separated
        'icd10_codes' => $request->icd10_codes,
        'cpt_codes' => $request->cpt_codes,
        'service_billed' => $request->service_billed,
        'billing_type' => $request->billing_type,
        'is_cms1500_generated' => $request->is_cms1500_generated,
        'case_won' => $request->case_won,
        'outcome' => $request->outcome,
        'reduction_accepted' => $request->reduction_accepted,
        'is_closed' => $request->is_closed,
        'closed_at' => $request->closed_at,
    ]);

    return response()->json($case, 201);
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
    public function show($caseId)
{
    // Fetch the case details by case_id
    $case = Cases::with(['patient', 'attorney']) // Assuming you have relationships defined
                ->where('case_id', $caseId)
                ->firstOrFail();

    // Decode the policy limit info from JSON
    $case->policy_limit_info = json_decode($case->policy_limit_info);

    // Fetch the referrals related to the case
    $referrals = Referral::where('referral_id', $case->primary_referral_id) // Assuming `case_id` is the relation key
                        ->get();
    $referrals = ReferralResource::collection($referrals);

    // Fetch ICD-10 code description (if available)
    $icdCodeDescription = $case->icd10_codes ? IcdCode::find($case->icd10_codes)->description : 'No ICD-10 code';

    // Fetch CPT codes descriptions (splitting the comma-separated values)
    $cptCodeDescriptions = [];
    if ($case->cpt_codes) {
        $cptCodeIds = explode(',', $case->cpt_codes);  // Split the comma-separated CPT code IDs
        $cptCodeDescriptions = CptCode::whereIn('id', $cptCodeIds)->pluck('description')->toArray();  // Fetch descriptions
    }

    // Return the data to the Inertia view
    return Inertia::render('panel/admin/cases/case-view', [
        'caseDetails' => $case,
        'icdCodeDescription' => $icdCodeDescription,
        'cptCodeDescriptions' => $cptCodeDescriptions,
        'referrals' => $referrals,
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
}
