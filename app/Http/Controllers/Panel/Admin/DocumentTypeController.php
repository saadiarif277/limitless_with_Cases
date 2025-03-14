<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Models\DocumentCategory;
use App\Models\DocumentType;
use App\Models\State;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentCategoryResource;
use App\Http\Resources\DocumentTypeResource;
use App\Http\Resources\StateResource;
use App\Http\Requests\DestroyDocumentTypeRequest;
use App\Http\Requests\StoreDocumentTypeRequest;
use App\Http\Requests\UpdateDocumentTypeRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('panel/admin/document-types/document-types-list', [
            'documentCategories' => DocumentCategoryResource::collection(DocumentCategory::all()),
            'documentTypes' => DocumentTypeResource::collection(
                DocumentType::query()
                    ->with(['documentCategory'])
                    ->when($request->filled('searchQuery'), function ($query) use ($request) {
                        $searchTerm = strtolower($request->get('searchQuery'));
                        $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"]);
                    })
                    ->orderBy('name')
                    ->paginate(10)
            ),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('panel/admin/document-types/document-types-create-edit', [
            'documentCategories' => DocumentCategoryResource::collection(DocumentCategory::all()),
            'states' => StateResource::collection(State::all()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentTypeRequest $request)
    {
        $documentType = DocumentType::create($request->safe()->only([
            'name',
            'description',
            'document_category_id',
        ]));

        $documentType->states()->sync($request->validated()['state_ids']);

        return to_route('panel.admin.document-types.show', [
            'document_type' => $documentType->getKey(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, DocumentType $documentType): Response
    {
        $documentType->load([
            'documentCategory',
            'states',
        ]);

        return Inertia::render('panel/admin/document-types/document-types-create-edit', [
            'documentCategories' => DocumentCategoryResource::collection(DocumentCategory::all()),
            'documentType' => new DocumentTypeResource($documentType),
            'states' => StateResource::collection(State::all()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentTypeRequest $request, DocumentType $documentType)
    {
        $documentType->update($request->safe()->only([
            'name',
            'description',
            'document_category_id',
        ]));

        $documentType->states()->sync($request->validated()['state_ids']);

        return to_route('panel.admin.document-types.show', [
            'document_type' => $documentType->getKey(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyDocumentTypeRequest $request, DocumentType $documentType)
    {
        $documentType->delete();
        return to_route('panel.admin.document-types.index');
    }
}
