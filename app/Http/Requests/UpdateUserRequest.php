<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class UpdateUserRequest extends FormRequest
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
        $rules = [
            // General Information
            'name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'nullable|string',
            'birthdate' => 'nullable|date_format:Y-m-d',

            // Location Information
            'address_line_1' => 'nullable|string',
            'address_line_2' => 'nullable|string',
            'city' => 'nullable|string',
            'state_id' => 'required|integer|exists:App\Models\State,state_id',
            'zip_code' => 'nullable|string',

            // Personal Health Information
            'gender' => 'nullable|string|in:male,female,other',
            'height' => 'nullable|string',
            'weight' => 'nullable|string',

            // Access & Security
            'password' => 'nullable|confirmed|min:6',
            'role_names' => 'nullable|array',
            'clinic_ids' => 'nullable|array',
            'law_firm_id' => 'nullable|integer|exists:App\Models\LawFirm,law_firm_id',
            'medical_specialty_id' => 'nullable|integer|exists:App\Models\MedicalSpecialty,medical_specialty_id',
        ];

        // Check if the user has the 'Patient' role
        if (in_array('Patient', $this->role_names)) {
            $rules['height'] = 'required|string';
            $rules['weight'] = 'required|string';
        }

        return $rules;
    }
}
