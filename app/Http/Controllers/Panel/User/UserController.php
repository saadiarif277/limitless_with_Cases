<?php

namespace App\Http\Controllers\Panel\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClinicResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\DestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Clinic;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getPatients(Request $request): Response
    {
        $currentUser = auth()->user();

        return Inertia::render('panel/user/users/patients-list', [
            'users' => UserResource::collection(
                User::query()
                    ->when($request->filled('searchQuery'), function ($query) use ($request) {
                        $searchTerm = strtolower($request->get('searchQuery'));
                        $query
                            ->orWhereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
                            ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchTerm}%"]);
                    })
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'Patient');
                    })
                    ->whereHas('referrals', function ($query) use ($currentUser) {
                        $query
                            /**
                             * Get the users who have referrals where the patient is
                             * the current user.
                             */
                            ->where('referrals.patient_user_id', $currentUser->getKey())

                            /**
                             * Get the users who have referrals where the doctor is the
                             * current user.
                             */
                            ->orWhere('referrals.doctor_user_id', $currentUser->getKey())

                            /**
                             * Get the users who have referrals where the clinic is within the
                             * current user's assigned clinics.
                             */
                            ->orWhereIn('referrals.clinic_id', $currentUser->clinics->pluck('clinic_id')->toArray())

                            /**
                             * Get the users who have referrals where the attorney is the current
                             * user or the attorney's law firm is the same as the current user.
                             */
                            ->orWhereHas('attorneyUser', function ($subQuery) use ($currentUser) {
                                $subQuery
                                    ->where('referrals.attorney_user_id', $currentUser->getKey())
                                    ->orWhereHas('lawFirm', function($altQuery) use ($currentUser) {
                                        $altQuery
                                            ->where('law_firms.law_firm_id', $currentUser->law_firm_id);
                                    });
                            });
                    })
                    ->orderBy('name')
                    ->paginate(10)
            ),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function showPatient(User $user)
    {
        $user->load([
            'clinics',
            'roles'
        ]);

        return Inertia::render('panel/user/users/patients-create-edit', [
            'clinics' => ClinicResource::collection(
                Clinic::query()
                    ->orderBy('name')
                    ->get()
            ),
            'roles' => RoleResource::collection(
                Role::query()
                ->orderBy('name')
                ->get()
            ),
            'states' => StateResource::collection(
                State::query()
                ->orderBy('name')
                ->get()
            ),
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            // General Information
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'phone_number' => $request->get('phone_number'),
            'birthdate' => $request->get('birthdate'),

            // Location Information
            'address_line_1' => $request->get('address_line_1'),
            'address_line_2' => $request->get('address_line_2'),
            'city' => $request->get('city'),
            'state_id' => $request->get('state_id'),
            'zip_code' => $request->get('zip_code'),
            
            // Personal Health Information
            'gender' => $request->get('gender'),
            'height' => $request->get('height'),
            'weight' => $request->get('weight'),

            // Access & Security
            'password' => bcrypt($request->get('password')),
        ]);

        $user->syncRoles($request->get('role_names'));
        $user->clinics()->sync($request->get('clinic_ids'));

        return to_route('panel.user.patients.show', [
            'user' => $user->getKey(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load([
            'clinics',
            'roles'
        ]);

        return Inertia::render('panel/user/users/users-create-edit', [
            'clinics' => ClinicResource::collection(
                Clinic::query()
                    ->orderBy('name')
                    ->get()
            ),
            'roles' => RoleResource::collection(
                Role::query()
                ->orderBy('name')
                ->get()
            ),
            'states' => StateResource::collection(
                State::query()
                ->orderBy('name')
                ->get()
            ),
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $payload = [];

        if ($request->filled('password')) {
            $payload['password'] = $request->get('password');
        }

        $user->update(array_merge($payload, [
            // General Information
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'phone_number' => $request->get('phone_number', $user->phone_number),
            'birthdate' => $request->get('birthdate', $user->birthdate),

            // Location Information
            'address_line_1' => $request->get('address_line_1', $user->address_line_1),
            'address_line_2' => $request->get('address_line_2', $user->address_line_2),
            'city' => $request->get('city', $user->city),
            'state_id' => $request->get('state_id', $user->state_id),
            'zip_code' => $request->get('zip_code', $user->zip_code),
            
            // Personal Health Information
            'gender' => $request->get('gender', $user->gender),
            'height' => $request->get('height', $user->height),
            'weight' => $request->get('weight', $user->weight),
        ]));

        return to_route('panel.user.patients.show', [
            'user' => $user->getKey(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyUserRequest $request, User $user)
    {
        $user->delete();
        return to_route('panel.user.patients.index');
    }
}
