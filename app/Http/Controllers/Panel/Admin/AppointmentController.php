<?php

namespace App\Http\Controllers\Panel\Admin;

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
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('panel/admin/appointments/appointments-list', [
            'appointments' => AppointmentResource::collection(
                Appointment::query()
                    ->when($request->filled('selected_clinic_ids'), function ($query) use ($request) {
                        $query->whereIn('clinic_id', $request->get('selected_clinic_ids'));
                    })
                    ->with('appointmentType')
                    ->with('clinic')
                    ->get()
            ),
            'clinics' => ClinicResource::collection(Clinic::orderBy('name')->get()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return Inertia::render('panel/admin/appointments/appointments-create-edit', [
            'appointmentTypes' => AppointmentTypeResource::collection(AppointmentType::all()),
            'clinics' => ClinicResource::collection(Clinic::all()),
            'patients' => UserResource::collection(
                User::query()
                    ->whereHas('roles', function ($query) {
                        $query->where('roles.name', 'Patient');
                    })
                    ->get()
            ),
            'referral' => $request->filled('referral_id')
                ? new ReferralResource(Referral::with(['clinic', 'patientUser'])->findOrFail($request->get('referral_id')))
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

        return to_route('panel.admin.appointments.show', [
            'appointment' => $appointment->getKey(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['referral']);

        return Inertia::render('panel/admin/appointments/appointments-create-edit', [
            'appointment' => new AppointmentResource($appointment),
            'appointmentTypes' => AppointmentTypeResource::collection(AppointmentType::all()),
            'patients' => UserResource::collection(User::all()),
            'clinics' => ClinicResource::collection(Clinic::all()),
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

        return to_route('panel.admin.appointments.show', [
            'appointment' => $appointment->getKey(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return to_route('panel.admin.appointments.index');
    }
}
