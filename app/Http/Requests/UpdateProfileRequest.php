<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            // General Information
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore(auth()->user()->getKey(), 'user_id')],
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
        ];
    }
}
