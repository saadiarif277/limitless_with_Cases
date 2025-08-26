<?php

namespace App\Http\Controllers\Panel\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentCategoryResource;
use App\Http\Resources\IcdCodeResource;
use App\Http\Resources\MedicalSpecialtyResource;
use App\Http\Resources\ReferralReasonResource;
use App\Http\Resources\ReferralStatusResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\CptCodeResource;
use App\Http\Resources\ReferralResource;
use App\Http\Requests\StoreReferralRequest;
use App\Http\Requests\UpdateReferralRequest;
use App\Http\Requests\DestroyReferralRequest;
use App\Models\DocumentCategory;
use App\Models\DocumentType;
use App\Models\Referral;
use App\Models\ReferralReason;
use App\Models\ReferralStatus;
use App\Models\State;
use App\Models\User;
use App\Models\MedicalSpecialty;
use App\Models\IcdCode;
use App\Models\CptCode;
use App\Models\ReductionRequest;
use App\Repositories\ReferralRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

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
        $userRole = $user->roles->first()->name;

        // Get referrals based on user role
        $referrals = $this->referralRepository->getItems($request);

        // Filter referrals based on user role and permissions
        $filteredReferrals = $this->filterReferralsByRole($referrals, $user, $userRole);

        return Inertia::render('panel/user/referrals/referrals-list', [
            'referrals' => ReferralResource::collection($filteredReferrals),
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
        $userRole = $user->roles->first()->name;

        // Check if user has permission to create referrals
        if (!in_array($userRole, ['Attorney', 'Doctor', 'Office Manager'])) {
            abort(403, 'Only attorneys, doctors, and office managers can create referrals.');
        }

        $roles = $user->roles;

        // Get document category IDs based on user role
        $documentCategoryIds = DB::table('pivot_document_categories_roles')
            ->whereIn('role_id', $roles->pluck('id')->toArray())
            ->pluck('document_category_id');

        // If no document categories found for the role, get all document categories
        // This ensures Attorneys can see all available document types
        if ($documentCategoryIds->isEmpty()) {
            $documentCategoryIds = \App\Models\DocumentCategory::pluck('document_category_id');
        }

        // Log document category information
        \Log::info('Referral Create - Document Categories', [
            'user_role' => $userRole,
            'role_ids' => $roles->pluck('id')->toArray(),
            'document_category_ids' => $documentCategoryIds->toArray(),
            'document_category_ids_count' => $documentCategoryIds->count(),
        ]);

        // Debug information
        \Log::info('Referral Create - User Info', [
            'user_id' => $user->user_id,
            'user_role' => $userRole,
            'user_state_id' => $user->state_id,
            'request_state_id' => $request->get('state_id'),
            'user_law_firm_id' => $user->lawFirm?->law_firm_id,
            'user_clinic_ids' => $user->clinics->pluck('clinic_id')->toArray(),
        ]);

        // Get the selected state (default to user's state if none selected)
        $selectedStateId = $request->get('state_id', $user->state_id);

        // Debug the selected state
        \Log::info('Referral Create - State Selection', [
            'selected_state_id' => $selectedStateId,
            'user_state_id' => $user->state_id,
            'request_state_id' => $request->get('state_id'),
        ]);

        // Check if user has required relationships
        try {
            $user->load(['clinics', 'lawFirm']);
            \Log::info('Referral Create - User Relationships Loaded', [
                'clinics_count' => $user->clinics->count(),
                'law_firm_loaded' => $user->lawFirm ? 'yes' : 'no',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading user relationships', [
                'error' => $e->getMessage(),
                'user_id' => $user->user_id
            ]);
        }

        // Check if states table exists and has data
        try {
            $statesCount = State::count();
            \Log::info('Referral Create - States Check', [
                'states_count' => $statesCount,
                'states_table_exists' => 'yes'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error checking states table', [
                'error' => $e->getMessage()
            ]);
        }

        // Get the data with simplified filtering
        $attorneysData = User::query()
            ->whereHas('roles', function($query) {
                $query->where('name', 'Attorney');
            })
            ->when($selectedStateId, function($query) use ($selectedStateId) {
                $query->whereHas('lawFirm', function($subQuery) use ($selectedStateId) {
                    $subQuery->where('law_firms.state_id', $selectedStateId);
                });
            })
            ->orderBy('name');

        $doctorsData = User::query()
            ->with(['clinics'])
            ->whereHas('roles', function($query) {
                $query->where('name', 'Doctor');
            })
            ->when($selectedStateId, function($query) use ($selectedStateId) {
                $query->whereHas('clinics', function($subQuery) use ($selectedStateId) {
                    $subQuery->where('clinics.state_id', $selectedStateId);
                });
            })
            ->orderBy('name');

        $patientsData = User::query()
            ->whereHas('roles', function($query) {
                $query->where('name', 'Patient');
            })
            ->when($selectedStateId, function($query) use ($selectedStateId) {
                $query->where('state_id', $selectedStateId);
            })
            ->orderBy('name');

        // Log the SQL queries for debugging
        \Log::info('Referral Create - SQL Queries', [
            'attorneys_sql' => $attorneysData->toSql(),
            'doctors_sql' => $doctorsData->toSql(),
            'patients_sql' => $patientsData->toSql(),
        ]);

        // Execute the queries with error handling
        try {
            $attorneysData = $attorneysData->get();
            $doctorsData = $doctorsData->get();
            $patientsData = $patientsData->get();
        } catch (\Exception $e) {
            \Log::error('Error executing queries in ReferralController::create', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback to empty collections if queries fail
            $attorneysData = collect();
            $doctorsData = collect();
            $patientsData = collect();
        }

        // Log the data counts
        \Log::info('Referral Create - Data Counts', [
            'attorneys_count' => $attorneysData->count(),
            'doctors_count' => $doctorsData->count(),
            'patients_count' => $patientsData->count(),
        ]);

        // Add fallback logic if no filtered results found, but respect attorney state restrictions
        if ($attorneysData->count() === 0) {
            \Log::warning('No attorneys found with current filters, falling back to all attorneys');
            try {
                $attorneysData = User::query()
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'Attorney');
                    })
                    ->orderBy('name')
                    ->get();
                \Log::info('Fallback attorneys count:', ['count' => $attorneysData->count()]);
            } catch (\Exception $e) {
                \Log::error('Error in fallback attorneys query', ['error' => $e->getMessage()]);
                $attorneysData = collect();
            }
        }

        if ($doctorsData->count() === 0) {
            \Log::warning('No doctors found with current filters, falling back to all doctors');
            try {
                $doctorsData = User::query()
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'Doctor');
                    })
                    ->orderBy('name')
                    ->get();
                \Log::info('Fallback doctors count:', ['count' => $doctorsData->count()]);
            } catch (\Exception $e) {
                \Log::error('Error in fallback doctors query', ['error' => $e->getMessage()]);
                $doctorsData = collect();
            }
        }

        if ($patientsData->count() === 0) {
            \Log::warning('No patients found with current filters, falling back to all patients');
            try {
                $patientsData = User::query()
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'Patient');
                    })
                    ->orderBy('name')
                    ->get();
                \Log::info('Fallback patients count:', ['count' => $patientsData->count()]);
            } catch (\Exception $e) {
                \Log::error('Error in fallback patients query', ['error' => $e->getMessage()]);
                $patientsData = collect();
            }
        }

        // Log final counts after fallback
        \Log::info('Referral Create - Final Data Counts', [
            'attorneys_count' => $attorneysData->count(),
            'doctors_count' => $doctorsData->count(),
            'patients_count' => $patientsData->count(),
        ]);

        // Test UserResource with a simple user
        $testUser = User::first();
        if ($testUser) {
            \Log::info('Test UserResource', [
                'test_user' => (new \App\Http\Resources\UserResource($testUser))->toArray(request())
            ]);
        }

        // Log raw data for debugging
        \Log::info('Referral Create - Raw Data Sample', [
            'attorneys_sample' => $attorneysData->take(2)->map(function($user) {
                return [
                    'user_id' => $user->user_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'state_id' => $user->state_id,
                    'law_firm_id' => $user->law_firm_id,
                ];
            }),
            'doctors_sample' => $doctorsData->take(2)->map(function($user) {
                return [
                    'user_id' => $user->user_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'state_id' => $user->state_id,
                    'clinics_count' => $user->clinics->count(),
                ];
            }),
            'patients_sample' => $patientsData->take(2)->map(function($user) {
                return [
                    'user_id' => $user->user_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'state_id' => $user->state_id,
                ];
            }),
        ]);

        try {
            return Inertia::render('panel/user/referrals/referrals-create', [
                'attorneys' => UserResource::collection($attorneysData),
                'doctors' => UserResource::collection($doctorsData),
                'physicians' => UserResource::collection(
                    User::query()
                        ->whereHas('roles', function($query) {
                            $query->where('name', 'Doctor');
                        })
                        ->orderBy('name')
                        ->get()
                ),
                                                        'documentCategories' => [
                'data' => (function() use ($userRole, $selectedStateId) {
                    // Debug: Log the initial parameters
                    \Log::info('Document Categories Debug - Initial', [
                        'user_role' => $userRole,
                        'selected_state_id' => $selectedStateId,
                        'has_state' => !empty($selectedStateId)
                    ]);

                    $query = DocumentCategory::query()
                        ->with(['documentTypes' => function ($query) use ($selectedStateId, $userRole) {
                            // For all roles, show all document types regardless of state
                            // State filtering was too restrictive and causing issues
                            // If state filtering is needed in the future, it should be implemented differently

                            // Debug: Log the document types query
                            \Log::info('Document Types Query Debug', [
                                'user_role' => $userRole,
                                'selected_state_id' => $selectedStateId,
                                'query_sql' => $query->toSql()
                            ]);

                            $query->addSelect([
                                'document_types.*',
                                // Add role-based upload permission flag - Fixed role names and permissions
                                \DB::raw("CASE
                                    WHEN document_types.is_generated = 1 THEN 0
                                    WHEN '{$userRole}' = 'Doctor' THEN 1
                                    WHEN '{$userRole}' = 'Attorney' THEN 1
                                    WHEN '{$userRole}' = 'Administrator' THEN 1
                                    WHEN '{$userRole}' = 'System' THEN 1
                                    WHEN '{$userRole}' = 'Office Manager' THEN 1
                                    ELSE 0
                                END as can_upload")
                            ]);
                        }])
                        // Temporarily removed whereHas to debug the issue
                        // ->whereHas('documentTypes')
                        ->orderBy('name');

                    $result = $query->get();

                    // Debug: Check raw database data
                    \Log::info('Raw Database Check', [
                        'total_document_categories' => \App\Models\DocumentCategory::count(),
                        'total_document_types' => \App\Models\DocumentType::count(),
                        'categories_with_whereHas' => $result->count(),
                        'all_categories' => \App\Models\DocumentCategory::all()->map(function($cat) {
                            return [
                                'id' => $cat->document_category_id,
                                'name' => $cat->name,
                                'has_document_types' => $cat->documentTypes()->count()
                            ];
                        })->toArray()
                    ]);

                    // Debug logging
                    \Log::info('Document Categories for User Role (Create)', [
                        'user_role' => $userRole,
                        'selected_state_id' => $selectedStateId,
                        'total_categories' => $result->count(),
                        'categories_with_docs' => $result->map(function($cat) {
                            return [
                                'id' => $cat->document_category_id,
                                'name' => $cat->name,
                                'document_types_count' => $cat->documentTypes->count(),
                                'document_types' => $cat->documentTypes->map(function($type) {
                                    return [
                                        'id' => $type->document_type_id,
                                        'name' => $type->name,
                                        'category_id' => $type->document_category_id,
                                        'is_generated' => $type->is_generated,
                                        'is_permanent' => $type->is_permanent,
                                        'can_upload' => $type->can_upload,
                                    ];
                                })->toArray()
                            ];
                        })->toArray()
                    ]);

                    return DocumentCategoryResource::collection($result);
                })()
            ],


                'medicalSpecialties' => \App\Http\Resources\MedicalSpecialtyResource::collection(
                    \App\Models\MedicalSpecialty::query()
                        ->orderBy('name')
                        ->get()
                ),


                'patients' => UserResource::collection($patientsData),
                'referralReasons' => $this->getReferralReasons(),
                'referralStatuses' => $this->getReferralStatuses(),
                'referralStates' => $this->getReferralStates(),
                'states' => $this->getStates(),
                'icdCodes' => $this->getIcdCodes(),
                'CptCodes' => (function() {
                    try {
                        // Check if table exists
                        $tableExists = \Schema::hasTable('cpt_codes');
                        \Log::info('CPT codes table exists:', ['exists' => $tableExists]);

                        if (!$tableExists) {
                            \Log::error('CPT codes table does not exist');
                            return [];
                        }

                        // Check table structure
                        $columns = \Schema::getColumnListing('cpt_codes');
                        \Log::info('CPT codes table columns:', $columns);

                        // Get CPT codes
                        $cptCodes = \App\Models\CptCode::all();
                        \Log::info('CPT Codes in controller:', [
                            'count' => $cptCodes->count(),
                            'first_code' => $cptCodes->first(),
                            'all_codes' => $cptCodes->toArray()
                        ]);

                        // Convert to array for frontend compatibility
                        return \App\Http\Resources\CptCodeResource::collection($cptCodes)->toArray(request());
                    } catch (\Exception $e) {
                        \Log::error('Error getting CPT codes in controller:', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        return [];
                    }
                })(),
                // Add selected state for frontend reference
                'selectedStateId' => $selectedStateId,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in ReferralController::create - Inertia render', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return a simple error response
            abort(500, 'Error loading referral creation form: ' . $e->getMessage());
        }
    }

    /**
     * Get filtered data by state for AJAX requests.
     */
    public function getDataByState(Request $request)
    {
        try {
            $user = auth()->user();
            $userRole = $user->roles->first()->name;
            $stateId = $request->get('state_id');

            \Log::info('AJAX getDataByState called', [
                'user_id' => $user->user_id,
                'user_role' => $userRole,
                'state_id' => $stateId,
                'request_data' => $request->all()
            ]);

            if (!$stateId) {
                return response()->json(['error' => 'State ID is required'], 400);
            }

            // Check if user has permission to create referrals
            if (!in_array($userRole, ['Attorney', 'Doctor', 'Office Manager'])) {
                abort(403, 'Only attorneys, doctors, and office managers can create referrals.');
            }

            // Get filtered data for the selected state
            $attorneysData = User::query()
                ->whereHas('roles', function($query) {
                    $query->where('name', 'Attorney');
                })
                ->whereHas('lawFirm', function($query) use ($stateId) {
                    $query->where('law_firms.state_id', $stateId);
                })
                ->orderBy('name')
                ->get();

            $doctorsData = User::query()
                ->with(['clinics'])
                ->whereHas('roles', function($query) {
                    $query->where('name', 'Doctor');
                })
                ->whereHas('clinics', function($query) use ($stateId) {
                    $query->where('clinics.state_id', $stateId);
                })
                ->orderBy('name')
                ->get();

            $patientsData = User::query()
                ->whereHas('roles', function($query) {
                    $query->where('name', 'Patient');
                })
                ->where('state_id', $stateId)
                ->orderBy('name')
                ->get();

            \Log::info('AJAX getDataByState results', [
                'attorneys_count' => $attorneysData->count(),
                'doctors_count' => $doctorsData->count(),
                'patients_count' => $patientsData->count(),
            ]);

            return response()->json([
                'attorneys' => UserResource::collection($attorneysData),
                'doctors' => UserResource::collection($doctorsData),
                'patients' => UserResource::collection($patientsData),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getDataByState', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReferralRequest $request)
    {
        $user = auth()->user();
        $userRole = $user->roles->first()->name;

        // Check if user has permission to create referrals
        if (!in_array($userRole, ['Attorney', 'Doctor', 'Office Manager'])) {
            abort(403, 'Only attorneys, doctors, and office managers can create referrals.');
        }

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
        $userRole = $user->roles->first()->name;

        // Check if user has permission to view this referral
        if (!$this->canViewReferral($user, $referral)) {
            abort(403, 'You do not have permission to view this referral.');
        }

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
            'documentCategories' => [
                'data' => DocumentCategoryResource::collection(
                    DocumentCategory::query()
                        ->with(['documentTypes' => function ($query) use ($referral, $userRole) {
                            // For all roles, show all document types regardless of state
                            // State filtering was too restrictive and causing issues
                            // If state filtering is needed in the future, it should be implemented differently

                            $query->addSelect([
                                'document_types.*',
                                // Add role-based upload permission flag - Fixed role names and permissions
                                \DB::raw("CASE
                                    WHEN document_types.is_generated = 1 THEN 0
                                    WHEN '{$userRole}' = 'Doctor' THEN 1
                                    WHEN '{$userRole}' = 'Attorney' THEN 1
                                    WHEN '{$userRole}' = 'Administrator' THEN 1
                                    WHEN '{$userRole}' = 'System' THEN 1
                                    WHEN '{$userRole}' = 'Office Manager' THEN 1
                                    ELSE 0
                                END as can_upload")
                            ]);
                        }])
                        // Temporarily removed whereHas to debug the issue
                        // ->whereHas('documentTypes')
                        ->orderBy('name')
                        ->get()
                )
            ],
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
        $user = auth()->user();
        $userRole = $user->roles->first()->name;

        // Check if user has permission to update this referral
        if (!in_array($userRole, ['Attorney', 'Doctor', 'Office Manager'])) {
            abort(403, 'Only attorneys, doctors, and office managers can update referrals.');
        }

        // Only attorneys, doctors, and case managers can update referrals
        if (!in_array($userRole, ['Attorney', 'Doctor', 'Office Manager'])) {
            abort(403, 'Only attorneys, doctors, and office managers can update referrals.');
        }

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
        $user = auth()->user();
        $userRole = $user->roles->first()->name;

        // Check if user has permission to delete this referral
        if (!$this->canViewReferral($user, $referral)) {
            abort(403, 'You do not have permission to delete this referral.');
        }

        // Only attorneys, doctors, and case managers can delete referrals
        if (!in_array($userRole, ['Attorney', 'Doctor', 'Office Manager'])) {
            abort(403, 'Only attorneys, doctors, and office managers can delete referrals.');
        }

        $referral->documents()->delete();
        $referral->delete();
        return to_route('panel.user.referrals.index');
    }

    /**
     * Filter referrals based on user role and permissions
     */
    private function filterReferralsByRole($referrals, $user, $userRole): Collection
    {
        $role = strtolower($userRole);
        $userId = $user->user_id;

        return $referrals->filter(function ($referral) use ($role, $userId, $user) {
            switch ($role) {
                case 'attorney':
                case 'office_manager':
                    // Attorneys and office managers can view referrals in their state
                    return $referral->state_id == $user->state_id ||
                           ($user->lawFirm && $referral->state_id == $user->lawFirm->state_id);
                case 'doctor':
                    // Doctors can view referrals they created, are assigned to, or in their clinics
                    return $referral->doctor_user_id == $userId ||
                           $referral->source_user_id == $userId ||
                           $user->clinics->contains('state_id', $referral->state_id);
                case 'patient':
                    // Patients can only view their own referrals
                    return $referral->patient_user_id == $userId;
                default:
                    return false;
            }
        });
    }

    /**
     * Check if user has permission to view a referral
     */
    private function canViewReferral($user, $referral): bool
    {
        $role = strtolower($user->roles->first()->name);
        $userId = $user->user_id;

        switch ($role) {
            case 'attorney':
            case 'office_manager':
                // Attorneys and office managers can view referrals in their state
                return $referral->state_id == $user->state_id ||
                       ($user->lawFirm && $referral->state_id == $user->lawFirm->state_id);
            case 'doctor':
                // Doctors can view referrals they created, are assigned to, or in their clinics
                return $referral->doctor_user_id == $userId ||
                       $referral->source_user_id == $userId ||
                       $user->clinics->contains('state_id', $referral->state_id);
            case 'patient':
                // Patients can only view their own referrals
                return $referral->patient_user_id == $userId;
            default:
                return false;
        }
    }

    /**
     * Get reduction requests for the authenticated user (Doctor role).
     */
    public function reductionRequests(Request $request): Response
    {
        $user = auth()->user();
        $userRole = $user->roles->first()->name;

        // Only doctors can view reduction requests
        if ($userRole !== 'Doctor') {
            abort(403, 'Only doctors can view reduction requests.');
        }

        $reductionRequests = ReductionRequest::query()
            ->with(['case', 'referral.patientUser', 'referral.attorneyUser'])
            ->whereHas('referral', function ($query) use ($user) {
                $query->where('doctor_user_id', $user->user_id);
            })
            ->where('doctor_decision', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('panel/user/referrals/reduction-requests', [
            'reductionRequests' => $reductionRequests,
        ]);
    }

    /**
     * Update doctor's decision on a reduction request.
     */
    public function updateReductionDecision(Request $request, ReductionRequest $reductionRequest)
    {
        $user = auth()->user();
        $userRole = $user->roles->first()->name;

        // Only doctors can update reduction decisions
        if ($userRole !== 'Doctor') {
            abort(403, 'Only doctors can update reduction decisions.');
        }

        // Verify the doctor owns this referral
        if ($reductionRequest->referral->doctor_user_id !== $user->user_id) {
            abort(403, 'You can only respond to reduction requests for your own referrals.');
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
            $referral->update(['referral_status_id' => 3]); // Assuming 3 is "Reduction Accepted"
        } elseif ($validated['doctor_decision'] === 'rejected') {
            $referral->update(['referral_status_id' => 4]); // Assuming 4 is "Reduction Rejected"
        }

        return response()->json([
            'message' => 'Reduction decision updated successfully',
            'data' => $reductionRequest->fresh(),
        ]);
    }

    /**
     * Helper method to get referral reasons safely
     */
    private function getReferralReasons()
    {
        try {
            return ReferralReasonResource::collection(
                ReferralReason::query()
                    ->orderBy('name', 'asc')
                    ->get()
            );
        } catch (\Exception $e) {
            \Log::error('Error getting referral reasons', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Helper method to get referral statuses safely
     */
    private function getReferralStatuses()
    {
        try {
            return ReferralStatusResource::collection(
                ReferralStatus::query()
                    ->orderBy('name', 'asc')
                    ->get()
            );
        } catch (\Exception $e) {
            \Log::error('Error getting referral statuses', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Helper method to get referral states safely
     */
    private function getReferralStates()
    {
        try {
            return StateResource::collection(
                State::query()
                    ->orderBy('name')
                    ->get()
            );
        } catch (\Exception $e) {
            \Log::error('Error getting referral states', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Helper method to get states safely
     */
    private function getStates()
    {
        try {
            return StateResource::collection(
                State::query()
                    ->with('documentTypes')
                    ->orderBy('name')
                    ->get()
            );
        } catch (\Exception $e) {
            \Log::error('Error getting states', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Helper method to get ICD codes safely
     */
    private function getIcdCodes()
    {
        try {
            return \App\Http\Resources\IcdCodeResource::collection(
                \App\Models\IcdCode::query()
                    ->orderBy('code')
                    ->get()
            );
        } catch (\Exception $e) {
            \Log::error('Error getting ICD codes', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Helper method to get CPT codes safely
     */
    private function getCptCodes()
    {
        try {
            \Log::info('Getting CPT codes...');

            // Check if table exists
            $tableExists = \Schema::hasTable('cpt_codes');
            \Log::info('CPT codes table exists:', ['exists' => $tableExists]);

            if (!$tableExists) {
                \Log::error('CPT codes table does not exist');
                return collect();
            }

            // Check table structure
            $columns = \Schema::getColumnListing('cpt_codes');
            \Log::info('CPT codes table columns:', $columns);

            // Check if table has data
            $count = \App\Models\CptCode::count();
            \Log::info('CPT codes count:', ['count' => $count]);

            if ($count === 0) {
                \Log::warning('CPT codes table is empty');
                return collect();
            }

            $cptCodes = \App\Models\CptCode::query()
                ->orderBy('code')
                ->get();

            \Log::info('CPT codes retrieved successfully:', ['count' => $cptCodes->count()]);

            return \App\Http\Resources\CptCodeResource::collection($cptCodes);
        } catch (\Exception $e) {
            \Log::error('Error getting CPT codes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect();
        }
    }
}
