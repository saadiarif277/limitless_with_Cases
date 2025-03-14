<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'referral_id' => 'nullable|integer|exists:App\Models\Referral,referral_id',
            'description' => 'nullable|string',
            'appointment_type_id' => 'required|integer|exists:App\Models\AppointmentType,appointment_type_id',
            'clinic_id' => 'required|integer|exists:App\Models\Clinic,clinic_id',
            'patient_user_id' => 'required|integer|exists:App\Models\User,user_id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
        ];
    }
}
