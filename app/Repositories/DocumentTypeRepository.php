<?php

namespace App\Repositories;

use App\Models\DocumentType;
use App\Http\Requests\DestroyDocumentTypeRequest;
use App\Http\Requests\StoreDocumentTypeRequest;
use App\Http\Requests\UpdateDocumentTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DocumentTypeRepository
{
    /**
     * Get all the document types that the authenticated user can access.
     */
    public function getItems(Request $request = null): Collection
    {
        $user = auth()->user();

        return DocumentType::query()
            ->get();
    }

    public function getItem(Request $request = null, DocumentType $documentType): DocumentType
    {
        return false;
    }

    public function store(StoreDocumentTypeRequest $request): DocumentType
    {
        return false;
    }

    public function update(UpdateDocumentTypeRequest $request, DocumentType $documentType): DocumentType
    {
        return false;
    }

    public function destroy(DestroyDocumentTypeRequest $request, DocumentType $documentType): bool
    {
        return false;
    }
}
