<?php

namespace App\Http\Controllers\Panel\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\AppointmentTypeResource;
use App\Http\Resources\ClinicResource;
use App\Http\Resources\ReferralResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Clinic;
use App\Models\Referral;
use App\Models\User;
use App\Repositories\AppointmentRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    private AppointmentRepository $appointmentRepository;

    /**
     * Create a new instance.
     */
    public function __construct(
        AppointmentRepository $appointmentRepository,
    ) {
        $this->appointmentRepository = $appointmentRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $user = auth()->user();

        return Inertia::render('panel/user/appointments/appointments-list', [
            'appointments' => AppointmentResource::collection($this->appointmentRepository->getItems($request)),
            'clinics' => ClinicResource::collection(
                Clinic::query()
                    ->whereIn('clinic_id', $user->clinics->pluck('clinic_id')->toArray())
                    ->get()
            ),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = auth()->user();

        $referral = $request->filled('referral_id')
            ? Referral::with(['clinic', 'patientUser'])->findOrFail($request->get('referral_id'))
            : NULL;

        return Inertia::render('panel/user/appointments/appointments-create-edit', [
            'appointmentTypes' => AppointmentTypeResource::collection(AppointmentType::all()),
            'clinics' => ClinicResource::collection(
                Clinic::query()
                    ->whereIn('clinic_id', array_merge($user->clinics->pluck('clinic_id')->toArray() ,[
                        $request->filled('referral_id')
                            ? $referral->clinic_id
                            : NULL,
                    ]))
                    ->get()
            ),
            'patients' => UserResource::collection(
                User::query()
                    ->whereHas('roles', function ($query) {
                        $query->where('roles.name', 'Patient');
                    })
                    ->get()
            ),
            'referral' => $request->filled('referral_id')
                ? new ReferralResource($referral)
                : NULL,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request)
    {
        $appointment = Appointment::create([
            'referral_id' => $request->get('referral_id'),
            'description' => $request->get('description'),
            'appointment_type_id' => $request->get('appointment_type_id'),
            'clinic_id' => $request->get('clinic_id'),
            'patient_user_id' => $request->get('patient_user_id'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
        ]);

        if ($request->filled('referral_id')) {
            $referral = Referral::findOrFail($request->get('referral_id'))
                ->update([
                    'appointment_id' => $appointment->getKey(),
                ]);
        }

        return to_route('panel.user.appointments.show', [
            'appointment' => $appointment->getKey(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $user = auth()->user();
        $appointment->load(['referral']);
        
        return Inertia::render('panel/user/appointments/appointments-create-edit', [
            'appointment' => new AppointmentResource($appointment),
            'appointmentTypes' => AppointmentTypeResource::collection(AppointmentType::all()),
            'patients' => UserResource::collection(User::all()),
            'clinics' => ClinicResource::collection(
                Clinic::query()
                    ->whereIn('clinic_id', $user->clinics->pluck('clinic_id')->toArray())
                    ->get()
            ),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $appointment->update([
            'description' => $request->get('description'),
            'appointment_type_id' => $request->get('appointment_type_id'),
            'clinic_id' => $request->get('clinic_id'),
            'patient_user_id' => $request->get('patient_user_id'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
        ]);

        return to_route('panel.user.appointments.show', [
            'appointment' => $appointment->getKey(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return to_route('panel.user.appointments.index');
    }
}
