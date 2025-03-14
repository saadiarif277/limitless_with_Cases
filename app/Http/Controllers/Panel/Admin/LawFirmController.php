<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\LawFirmResource;
use App\Http\Resources\StateResource;
use App\Http\Requests\DestroyLawFirmRequest;
use App\Http\Requests\StoreLawFirmRequest;
use App\Http\Requests\UpdateLawFirmRequest;
use App\Models\LawFirm;
use App\Models\State;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LawFirmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('panel/admin/law-firms/law-firms-list', [
            'law-firms' => LawFirmResource::collection(
                LawFirm::query()
                    ->when($request->filled('searchQuery'), function ($query) use ($request) {
                        $searchTerm = strtolower($request->get('searchQuery'));
                        $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"]);
                    })
                    ->with('state')
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
        return Inertia::render('panel/admin/law-firms/law-firms-create-edit', [
            'states' => StateResource::collection(
                State::query()
                    ->orderBy('name')
                    ->get()
            ),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLawFirmRequest $request)
    {
        $lawFirm = LawFirm::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'phone_number' => $request->get('phone_number'),
            'address_line_1' => $request->get('address_line_1'),
            'address_line_2' => $request->get('address_line_2'),
            'city' => $request->get('city'),
            'state_id' => $request->get('state_id'),
            'zip_code' => $request->get('zip_code'),
        ]);

        return to_route('panel.admin.law-firms.show', [
            'law_firm' => $lawFirm->getKey(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, LawFirm $lawFirm): Response
    {
        return Inertia::render('panel/admin/law-firms/law-firms-create-edit', [
            'law-firm' => new LawFirmResource($lawFirm),
            'states' => StateResource::collection(
                State::query()
                    ->orderBy('name')
                    ->get()
            ),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLawFirmRequest $request, LawFirm $lawFirm)
    {
        $lawFirm->update([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'phone_number' => $request->get('phone_number'),
            'address_line_1' => $request->get('address_line_1'),
            'address_line_2' => $request->get('address_line_2'),
            'city' => $request->get('city'),
            'state_id' => $request->get('state_id'),
            'zip_code' => $request->get('zip_code'),
        ]);

        return to_route('panel.admin.law-firms.show', [
            'law_firm' => $lawFirm->getKey(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyLawFirmRequest $request, LawFirm $lawFirm)
    {
        $lawFirm->delete();
        return to_route('panel.admin.law-firms.index');
    }
}
