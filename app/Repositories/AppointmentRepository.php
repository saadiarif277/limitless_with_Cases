<?php

namespace App\Repositories;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AppointmentRepository
{
    /**
     * Get all the appointments that the authenticated user can access.
     */
    public function getItems(Request $request = null): Collection
    {
        $user = auth()->user();

        return Appointment::query()
            ->with('appointmentType')
            ->with('clinic')
            ->when($request->filled('selected_clinic_ids'), function ($query) use ($request) {
                $query->whereIn('clinic_id', $request->get('selected_clinic_ids'));
            })
            ->whereIn('clinic_id', $user->clinics->pluck('clinic_id')->toArray())
            ->orWhere('patient_user_id', $user->getKey())
            ->get();
    }
}
