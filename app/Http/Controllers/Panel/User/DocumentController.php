<?php

namespace App\Http\Controllers\Panel\User;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        $document->delete();
        return;
    }

    /**
     * Download the specified resource from storage.
     */
    public function download(Document $document)
    {
        try {
            // Check if document exists
            if (!$document) {
                abort(404, 'Document not found');
            }

            // Check if path is set
            if (!$document->path) {
                abort(404, 'Document path not found');
            }

            $pathToFile = storage_path('app/' . $document->path);

            // Check if file exists
            if (!file_exists($pathToFile)) {
                abort(404, 'Document file not found on disk');
            }

            // Check if file is readable
            if (!is_readable($pathToFile)) {
                abort(500, 'Document file is not readable');
            }

            return response()->download($pathToFile, $document->name ?? 'document');
        } catch (\Exception $e) {
            \Log::error('Document download error', [
                'document_id' => $document->document_id ?? 'unknown',
                'path' => $document->path ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, 'Error downloading document: ' . $e->getMessage());
        }
    }
}
