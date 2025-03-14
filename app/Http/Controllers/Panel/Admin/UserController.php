<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClinicResource;
use App\Http\Resources\LawFirmResource;
use App\Http\Resources\MedicalSpecialtyResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\DestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Clinic;
use App\Models\LawFirm;
use App\Models\MedicalSpecialty;
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
    public function index(Request $request): Response
    {
        return Inertia::render('panel/admin/users/users-list', [
            'users' => UserResource::collection(
                User::query()
                    ->when($request->filled('searchQuery'), function ($query) use ($request) {
                        $searchTerm = strtolower($request->get('searchQuery'));
                        $query
                            ->orWhereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
                            ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchTerm}%"]);
                    })
                    ->orderBy('name')
                    ->paginate(10)
            ),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('panel/admin/users/users-create-edit', [
            'clinics' => ClinicResource::collection(
                Clinic::query()
                    ->orderBy('name')
                    ->get()
            ),
            'lawFirms' => LawFirmResource::collection(
                LawFirm::query()
                    ->orderBy('name')
                    ->get()
            ),
            'medicalSpecialties' => MedicalSpecialtyResource::collection(
                MedicalSpecialty::query()
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
            'law_firm_id' => $request->get('law_firm_id'),
            'medical_specialty_id' => $request->get('medical_specialty_id'),
        ]);

        $user->syncRoles($request->get('role_names'));
        $user->clinics()->sync($request->get('clinic_ids'));

        return to_route('panel.admin.users.show', [
            'user' => $user->getKey(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $user): Response
    {
        $user->load([
            'clinics',
            'roles',
            'lawFirm',
            'medicalSpecialty',
        ]);

        return Inertia::render('panel/admin/users/users-create-edit', [
            'clinics' => ClinicResource::collection(
                Clinic::query()
                    ->orderBy('name')
                    ->get()
            ),
            'lawFirms' => LawFirmResource::collection(
                LawFirm::query()
                    ->orderBy('name')
                    ->get()
            ),
            'medicalSpecialties' => MedicalSpecialtyResource::collection(
                MedicalSpecialty::query()
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
            'law_firm_id' => $request->get('law_firm_id'),
            'medical_specialty_id' => $request->get('medical_specialty_id'),
        ]));

        $user->syncRoles($request->get('role_names'));
        $user->clinics()->sync($request->get('clinic_ids'));

        return to_route('panel.admin.users.show', [
            'user' => $user->getKey(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyUserRequest $request, User $user)
    {
        $user->delete();
        return to_route('panel.admin.users.index');
    }
}
