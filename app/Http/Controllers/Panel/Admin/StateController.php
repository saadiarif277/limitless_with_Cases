<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentTypeResource;
use App\Http\Resources\StateResource;
use App\Http\Requests\DestroyStateRequest;
use App\Http\Requests\StoreStateRequest;
use App\Http\Requests\UpdateStateRequest;
use App\Models\DocumentType;
use App\Models\State;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('panel/admin/states/states-list', [
            'states' => StateResource::collection(
                State::query()
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
        return Inertia::render('panel/admin/states/states-create-edit', [
            'document_types' => DocumentTypeResource::collection(DocumentType::all()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStateRequest $request)
    {
        $state = State::create([
            'name' => $request->get('name'),
        ]);

        $state->syncDocumentTypes($request->get('document_type_ids'));

        return to_route('panel.admin.states.show', [
            'state' => $state->getKey(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, State $state): Response
    {
        $state->load('documentTypes');

        return Inertia::render('panel/admin/states/states-create-edit', [
            'state' => new StateResource($state),
            'documentTypes' => DocumentTypeResource::collection(
                DocumentType::query()
                    ->orderBy('name')
                    ->get()
            ),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStateRequest $request, State $state)
    {
        $state->update([
            'name' => $request->get('name'),
        ]);

        $state->documentTypes()->sync($request->get('document_type_ids'));

        return to_route('panel.admin.states.show', [
            'state' => $state->getKey(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyStateRequest $request, State $state)
    {
        $state->delete();
        return to_route('panel.admin.states.index');
    }
}
