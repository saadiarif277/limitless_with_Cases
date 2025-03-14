<?php

namespace App\Http\Requests;

use App\Models\Clinic;
use App\Models\DocumentType;
use App\Models\User;
use App\Models\LawFirm;
use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class StoreReferralRequest extends FormRequest
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
        $allowedDocumentTypes = rtrim(DocumentType::get()->reduce(function ($carry, $item) {
            return $carry . $item->getKey() . ',';
        }, 'array:'), ',');

        return [
            'referral_date' => 'required|date',
            'referral_status_id' => 'required|integer|exists:App\Models\ReferralStatus,referral_status_id',
            'state_id' => 'required|integer|exists:App\Models\State,state_id',
            'documents' => "nullable|{$allowedDocumentTypes}",
            'documents.*' => 'file|max:51200',
            'injury_date' => 'required|date',

            // Optional Relationships
            'appointment_id' => 'nullable|integer|exists:App\Models\Appointment,appointment_id',
            'source_user_id' => 'nullable|integer|exists:App\Models\User,user_id',

            // Required Relationships
            'clinic_id' => 'nullable|integer|exists:App\Models\Clinic,clinic_id',
            'patient_user_id' => 'nullable|integer|exists:App\Models\User,user_id',
            'attorney_user_id' => 'nullable|integer|exists:App\Models\User,user_id',
            'doctor_user_id' => 'nullable|integer|exists:App\Models\User,user_id',
            'referral_reason_ids' => 'required|array',
            'referral_reason_ids.*' => 'integer|exists:App\Models\ReferralReason,referral_reason_id',

            // Doctor
            'doctor' => 'required|array',
            'doctor.user_id' => 'nullable|integer|exists:App\Models\User,user_id',
            'doctor.name' => 'required_without:doctor.user_id|nullable|string',
            'doctor.email' => 'required_without:doctor.user_id|nullable|email',
            'doctor.phone_number' => 'nullable|string',
            'doctor.notes' => 'nullable|string',
            'doctor.medical_specialty_id' => 'required_without:doctor.user_id|integer|exists:App\Models\MedicalSpecialty,medical_specialty_id',

            // Doctor/Clinic
            'doctor.clinic' => 'required_without:doctor.user_id|nullable|array',
            'doctor.clinic.name' => 'required_without:doctor.user_id|nullable|string',
            'doctor.clinic.email' => 'required_without:doctor.user_id|nullable|email',
            'doctor.clinic.phone_number' => 'nullable|string',
            'doctor.clinic.address_line_1' => 'nullable|string',
            'doctor.clinic.address_line_2' => 'nullable|string',
            'doctor.clinic.city' => 'nullable|string',
            'doctor.clinic.state_id' => 'required_without:doctor.user_id|nullable|integer|exists:App\Models\State,state_id',
            'doctor.clinic.zip_code' => 'nullable|string',

            // Patient
            'patient' => 'required|array',
            'patient.user_id' => 'nullable|integer|exists:App\Models\User,user_id',
            'patient.name' => 'required_without:patient.user_id|nullable|string',
            'patient.email' => 'required_without:patient.user_id|nullable|email',
            'patient.phone_number' => 'nullable|string',
            'patient.height' => 'nullable|string',
            'patient.weight' => 'nullable|string',
            'patient.gender' => 'nullable|string',
            'patient.birthdate' => 'nullable|date',
            'patient.injury_date' => 'required|date',
            'patient.address_line_1' => 'nullable|string',
            'patient.address_line_2' => 'nullable|string',
            'patient.city' => 'nullable|string',
            'patient.state_id' => 'required_without:patient.user_id|nullable|integer|exists:App\Models\State,state_id',
            'patient.zip_code' => 'nullable|string',
            'patient.referral_reason_ids' => 'required|array',
            'patient.referral_reason_ids.*' => 'integer|exists:App\Models\ReferralReason,referral_reason_id',

            // Attorney
            'attorney' => 'required|array',
            'attorney.user_id' => 'nullable|integer|exists:App\Models\User,user_id',
            'attorney.name' => 'required_without:attorney.user_id|nullable|string',
            'attorney.email' => 'required_without:attorney.user_id|nullable|email',
            'attorney.phone_number' => 'nullable|string',

            // Attorney/LawFirm
            'attorney.law_firm' => 'required_without:attorney.user_id|nullable|array',
            'attorney.law_firm.name' => 'required_without:attorney.user_id|nullable|string',
            'attorney.law_firm.email' => 'required_without:attorney.user_id|nullable|email',
            'attorney.law_firm.phone_number' => 'nullable|string',
            'attorney.law_firm.address_line_1' => 'nullable|string',
            'attorney.law_firm.address_line_2' => 'nullable|string',
            'attorney.law_firm.city' => 'nullable|string',
            'attorney.law_firm.state_id' => 'required_without:attorney.user_id|nullable|integer|exists:App\Models\State,state_id',
            'attorney.law_firm.zip_code' => 'nullable|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Doctor
            'doctor.name.required_without' => 'The doctor name field is required.',
            'doctor.email.required_without' => 'The doctor email field is required.',
            'doctor.email.unique' => 'The email has already been taken.',
            'doctor.medical_specialty_id.required_without' => 'The doctor medical specialty is required.',

            // Doctor/Clinic
            'doctor.clinic.name.required_without' => 'The clinic name field is required.',
            'doctor.clinic.email.required_without' => 'The clinic email field is required.',
            'doctor.clinic.email.unique' => 'The email has already been taken.',
            'doctor.clinic.state_id.required_without' => 'The clinic state field is required.',

            // Patient
            'patient.name.required_without' => 'The patient name field is required.',
            'patient.email.required_without' => 'The patient email field is required.',
            'patient.email.unique' => 'The email has already been taken.',
            'patient.injury_date.required_without' => 'The patient injury date field is required.',
            'patient.referral_reason_ids.required' => 'Please select reasons for the referral.',

            // Attorney
            'attorney.name.required_without' => 'The attorney name field is required.',
            'attorney.email.required_without' => 'The attorney email field is required.',
            'attorney.email.unique' => 'The email has already been taken.',

            // Attorney/LawFirm
            'attorney.law_firm.name.required_without' => 'The law firm name field is required.',
            'attorney.law_firm.email.required_without' => 'The law firm email field is required.',
            'attorney.law_firm.email.unique' => 'The email has already been taken.',
            'attorney.law_firm.state_id.required_without' => 'The law firm state field is required.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'referral_reason_ids' => array_values($this->patient['referral_reason_ids']),
            'injury_date' => $this->patient['injury_date'],
            'patient_user_id' => $this->patient['user_id'] ?? null,
            'doctor_user_id' => $this->doctor['user_id'] ?? null,
            'attorney_user_id' => $this->attorney['user_id'] ?? null,
            'clinic_id' => $this->doctor['clinic']['clinic_id'] ?? null,
        ]);
    }
}
