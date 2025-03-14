<?php

namespace App\Http\Controllers\Panel\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentCategoryResource;
use App\Http\Resources\ReferralResource;
use App\Http\Resources\ReferralReasonResource;
use App\Http\Resources\ReferralStatusResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\DestroyReferralRequest;
use App\Http\Requests\StoreReferralRequest;
use App\Http\Requests\UpdateReferralRequest;
use App\Repositories\ReferralRepository;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentType;
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
use DB;

class ReferralController extends Controller
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
        $user = auth()->user();

        return Inertia::render('panel/user/referrals/referrals-list', [
            'referrals' => ReferralResource::collection($this->referralRepository->getItems($request)),
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
        $user = auth()->user();
        $roles = $user->roles;
        $documentCategoryIds = DB::table('pivot_document_categories_roles')
            ->whereIn('role_id', $roles->pluck('id')->toArray())
            ->pluck('document_category_id');

        return Inertia::render('panel/user/referrals/referrals-create', [
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
                    ->with(['clinics' => function ($query) use ($request) {
                        $query->where('clinics.state_id', $request->get('state_id', 0));
                    }])
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
                    ->whereIn('document_category_id', $documentCategoryIds)
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
            'referralStates' => StateResource::collection(
                State::query()
                    ->whereIn('state_id', array_merge(
                        [$user->state_id], 
                        $user->clinics->pluck('state_id')->toArray(),
                        ($user->lawFirm && $user->lawFirm->state_id) ? [$user->lawFirm->state_id] : [],
                    ))
                    ->orderBy('name')
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
    public function store(StoreReferralRequest $request)
    {
        $referral = $this->referralRepository->create($request);

        return to_route('panel.user.referrals.show', [
            'referral' => $referral->getKey(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Referral $referral): Response
    {
        $user = auth()->user();
        $roles = $user->roles;
        $documentCategoryIds = DB::table('pivot_document_categories_roles')
            ->whereIn('role_id', $roles->pluck('id')->toArray())
            ->pluck('document_category_id');

        $referral->load([
            'appointment',
            'attorneyUser.lawFirm.state',
            'clinic.state',
            'documents',
            'doctorUser.state',
            'patientUser.state',
            'referralReasons',
            'referralStatus',
        ]);

        return Inertia::render('panel/user/referrals/referrals-edit', [
            'referral' => new ReferralResource($referral),
            'attorneys' => UserResource::collection(
                User::query()
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'Attorney');
                    })
                    ->whereHas('lawFirm', function ($query) use ($referral) {
                        $query->where('law_firms.law_firm_id', $referral->state_id);
                    })
                    ->orderBy('name')
                    ->get()
            ),
            'doctors' => UserResource::collection(
                User::query()
                    ->with(['clinics' => function ($query) use ($referral) {
                        $query->where('clinics.state_id', $referral->state_id);
                    }])
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
                        $documentTypeIds = $referral
                            ->state
                            ->documentTypes
                            ->pluck('document_type_id')
                            ->toArray();

                        $query
                            ->whereIn('document_types.document_type_id', $documentTypeIds);
                    })
                    ->whereHas('documentTypes')
                    ->whereIn('document_category_id', $documentCategoryIds)
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
        $referral = $this->referralRepository->update($request, $referral);

        return to_route('panel.user.referrals.show', [
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
        return to_route('panel.user.referrals.index');
    }
}
