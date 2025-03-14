<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClinicResource;
use App\Http\Resources\StateResource;
use App\Http\Requests\DestroyClinicRequest;
use App\Http\Requests\StoreClinicRequest;
use App\Http\Requests\UpdateClinicRequest;
use App\Models\Clinic;
use App\Models\State;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClinicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('panel/admin/clinics/clinics-list', [
            'clinics' => ClinicResource::collection(
                Clinic::query()
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
        return Inertia::render('panel/admin/clinics/clinics-create-edit', [
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
    public function store(StoreClinicRequest $request)
    {
        $clinic = Clinic::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'phone_number' => $request->get('phone_number'),
            'address_line_1' => $request->get('address_line_1'),
            'address_line_2' => $request->get('address_line_2'),
            'city' => $request->get('city'),
            'state_id' => $request->get('state_id'),
            'zip_code' => $request->get('zip_code'),
            'price_read' => $request->get('price_read'),
            'price_scan' => $request->get('price_scan'),
        ]);

        return to_route('panel.admin.clinics.show', [
            'clinic' => $clinic->getKey(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Clinic $clinic): Response
    {
        return Inertia::render('panel/admin/clinics/clinics-create-edit', [
            'clinic' => new ClinicResource($clinic),
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
    public function update(UpdateClinicRequest $request, Clinic $clinic)
    {
        $clinic->update([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'phone_number' => $request->get('phone_number'),
            'address_line_1' => $request->get('address_line_1'),
            'address_line_2' => $request->get('address_line_2'),
            'city' => $request->get('city'),
            'state_id' => $request->get('state_id'),
            'zip_code' => $request->get('zip_code'),
            'price_read' => $request->get('price_read'),
            'price_scan' => $request->get('price_scan'),
        ]);

        return to_route('panel.admin.clinics.show', [
            'clinic' => $clinic->getKey(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyClinicRequest $request, Clinic $clinic)
    {
        $clinic->delete();
        return to_route('panel.admin.clinics.index');
    }
}
