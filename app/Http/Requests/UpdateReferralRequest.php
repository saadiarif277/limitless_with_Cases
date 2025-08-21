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
        $user = auth()->user();
        $userRole = $user->roles->first()->name;

        // Only attorneys, doctors, and case managers can update referrals
        return in_array($userRole, ['Attorney', 'Doctor', 'Case_manager']);
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

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = auth()->user();
            $userRole = $user->roles->first()->name;

            if ($this->has('documents') && is_array($this->documents)) {
                foreach ($this->documents as $documentTypeId => $document) {
                    if (!$document) continue;

                    $documentType = DocumentType::find($documentTypeId);
                    if (!$documentType) continue;

                    // Check if user has permission to upload this document type
                    $canUpload = $this->canUserUploadDocumentType($userRole, $documentType);

                    if (!$canUpload) {
                        $validator->errors()->add(
                            "documents.{$documentTypeId}",
                            "You don't have permission to upload {$documentType->name} documents."
                        );
                    }
                }
            }
        });
    }

    /**
     * Check if user can upload a specific document type based on role
     */
    private function canUserUploadDocumentType(string $userRole, DocumentType $documentType): bool
    {
        // System-generated documents cannot be uploaded
        if ($documentType->is_generated) {
            return false;
        }

        // Role-based permissions
        switch ($userRole) {
            case 'Doctor':
                return $documentType->document_category_id == \App\Models\DocumentCategory::MEDICAL;

            case 'Attorney':
                return $documentType->document_category_id == \App\Models\DocumentCategory::FINANCIAL;

            case 'Admin':
            case 'Case_manager':
                return true;

            default:
                return false;
        }
    }
}
