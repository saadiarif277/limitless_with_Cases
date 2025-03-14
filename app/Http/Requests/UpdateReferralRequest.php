<?php

namespace App\Http\Requests;

use App\Models\DocumentType;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReferralRequest extends FormRequest
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
            'source_user_id' => 'required|integer|exists:App\Models\User,user_id',
            'documents' => "nullable|{$allowedDocumentTypes}",
            'documents.*' => 'file|max:51200',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Filter out null and empty string values from the 'documents' array
        $documents = array_filter($this->documents, function ($value) {
            return !is_null($value) && $value !== '';
        });

        // Update the request data
        $this->merge([
            'documents' => $documents
        ]);
    }
}
