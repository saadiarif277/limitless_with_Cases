<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentCategoryResource;
use App\Http\Resources\MedicalSpecialtyResource;
use App\Http\Resources\ReferralResource;
use App\Http\Resources\IcdCodeResource;
use App\Http\Resources\CptCodeResource;
use App\Http\Resources\ReferralReasonResource;
use App\Http\Resources\ReferralStatusResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\DestroyReferralRequest;
use App\Http\Requests\StoreReferralRequest;
use App\Http\Requests\UpdateReferralRequest;
use App\Repositories\ReferralRepository;
use App\Repositories\CaseRepository;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentType;
use App\Models\MedicalSpecialty;
use App\Models\Referral;
use App\Models\ReferralReason;
use App\Models\ReferralStatus;
use App\Models\State;
use App\Models\IcdCode;
use App\Models\CptCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;
class ReferralController extends Controller
{
    private ReferralRepository $referralRepository;

    /**
     * Create a new instance.
     */
    public function __construct(ReferralRepository $referralRepository,CaseRepository $caseRepository)
    {
        $this->referralRepository = $referralRepository;
        $this->caseRepository = $caseRepository;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {


        return Inertia::render('panel/admin/referrals/referrals-list', [
            'referrals' => ReferralResource::collection(
                Referral::query()
                    ->with([
                        'appointment',
                        'attorneyUser.lawFirm',
                        'clinic',
                        'doctorUser.medicalSpecialty',
                        'patientUser',
                        'referralStatus',
                    ])
                    ->leftJoin('users as attorney_users', 'referrals.attorney_user_id', '=', 'attorney_users.user_id')
                    ->leftJoin('users as doctor_users', 'referrals.doctor_user_id', '=', 'doctor_users.user_id')
                    ->leftJoin('users as patient_users', 'referrals.patient_user_id', '=', 'patient_users.user_id')
                    ->leftJoin('clinics', 'referrals.clinic_id', '=', 'clinics.clinic_id')
                    ->when($request->filled('searchQuery'), function ($query) use ($request) {
                        $searchTerm = strtolower($request->get('searchQuery'));
                        $query->where(function ($subquery) use ($searchTerm) {
                            $subquery
                                ->orWhereRaw('LOWER(attorney_users.name) LIKE ?', ["%{$searchTerm}%"])
                                ->orWhereRaw('LOWER(attorney_users.email) LIKE ?', ["%{$searchTerm}%"])
                                ->orWhereRaw('LOWER(doctor_users.name) LIKE ?', ["%{$searchTerm}%"])
                                ->orWhereRaw('LOWER(doctor_users.email) LIKE ?', ["%{$searchTerm}%"])
                                ->orWhereRaw('LOWER(patient_users.name) LIKE ?', ["%{$searchTerm}%"])
                                ->orWhereRaw('LOWER(patient_users.email) LIKE ?', ["%{$searchTerm}%"])
                                ->orWhereRaw('LOWER(clinics.name) LIKE ?', ["%{$searchTerm}%"])
                                ->orWhereRaw('LOWER(clinics.email) LIKE ?', ["%{$searchTerm}%"]);
                        });
                    })
                    ->when($request->filled('referralStatusId'), function ($query) use ($request) {
                        $query->where('referrals.referral_status_id', $request->get('referralStatusId'));
                    })
                    ->orderBy('referral_date', 'desc')
                    ->orderBy('referrals.referral_id', 'desc')
                    ->paginate(10)
            ),
            'referralStatuses' => ReferralStatusResource::collection(
                ReferralStatus::query()
                    ->withCount('referrals')
                    ->orderBy('order', 'asc')
                    ->get()
            ),

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('panel/admin/referrals/referrals-create', [
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
                        // For admin users, show all document types regardless of state
                        // State filtering was too restrictive and causing issues
                        // If state filtering is needed in the future, it should be implemented differently

                        // Only filter out generated documents
                        $query->where('document_types.is_generated', false);
                    })
                    // Temporarily removed whereHas to debug the issue
                    // ->whereHas('documentTypes')
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
            'icdCodes' => IcdCodeResource::collection(
                IcdCode::all()
            ),
            'CptCodes' => CptCodeResource::collection(
                    CptCode::all()
            ),
           'physicians' => UserResource::collection(
                User::query()
                    ->whereHas('roles', function ($query) {
                        $query->where('name', 'Doctor');
                    })
                    ->orderBy('name')
                    ->get()
            ),

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReferralRequest $request)
    {


        $referral = $this->referralRepository->create($request);
        // Ensure the referral creation was successful


        // Add the referral ID to the request data for the case
        $request->merge(['primary_referral_id' => $referral->getKey()]);
        $case = $this->caseRepository->create($request);

        return to_route('panel.admin.referrals.show', [
            'referral' => $referral->getKey(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Referral $referral): Response
    {
        $referral->load([
            'appointment',
            'attorneyUser.lawFirm.state',
            'clinic.state',
            'documents',
            'doctorUser.state',
            'doctorUser.medicalSpecialty',
            'patientUser.state',
            'referralReasons',
            'referralStatus',
        ]);

        return Inertia::render('panel/admin/referrals/referrals-edit', [
            'referral' => new ReferralResource($referral),
            'attorneys' => UserResource::collection(
                User::query()
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'Attorney');
                    })
                    ->whereHas('lawFirm', function ($query) use ($referral) {
                        $query->where('law_firms.state_id', $referral->state_id);
                    })
                    ->orderBy('name')
                    ->get()
            ),
            'doctors' => UserResource::collection(
                User::query()
                    ->with('clinics')
                    ->whereHas('clinics', function ($query) use ($referral) {
                        $query->where('clinics.state_id', $referral->state_id);
                    })
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'Doctor');
                    })
                    ->orderBy('name')
                    ->get()
            ),
            'documentCategories' => DocumentCategoryResource::collection(
                DocumentCategory::query()
                    ->with('documentTypes', function ($query) use ($referral) {
                        // For admin users, show all document types regardless of state
                        // State filtering was too restrictive and causing issues
                        // If state filtering is needed in the future, it should be implemented differently
                    })
                    // Temporarily removed whereHas to debug the issue
                    // ->whereHas('documentTypes')
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
                    ->orderBy('name')
                    ->get()
            ),
            'sources' => UserResource::collection(
                User::query()
                    ->whereHas('roles', function($query) {
                        $query->whereNotIn('name', [
                            'System',
                            'Administrator',
                            'Funding Company',
                            'Office Manager',
                            'Patient',
                        ]);
                    })
                    ->orderBy('name')
                    ->get()
            ),
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
