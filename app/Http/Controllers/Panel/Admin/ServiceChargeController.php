<?php

namespace App\Http\Controllers;

use App\Models\ServiceCharge;
use Illuminate\Http\Request;

class ServiceChargeController extends Controller
{
    public function index()
    {
        $serviceCharges = ServiceCharge::all();
        return response()->json($serviceCharges);
    }

    public function store(Request $request)
    {
        $request->validate([
            'chargeDescription' => 'required|string|max:255',
            'amount' => 'required|numeric',
        ]);

        $serviceCharge = ServiceCharge::create($request->all());
        return response()->json($serviceCharge);
    }

    public function show($id)
    {
        $serviceCharge = ServiceCharge::findOrFail($id);
        return response()->json($serviceCharge);
    }

    public function update(Request $request, $id)
    {
        $serviceCharge = ServiceCharge::findOrFail($id);
        $request->validate([
            'chargeDescription' => 'required|string|max:255',
            'amount' => 'required|numeric',
        ]);

        $serviceCharge->update($request->all());
        return response()->json($serviceCharge);
    }

    public function destroy($id)
    {
        $serviceCharge = ServiceCharge::findOrFail($id);
        $serviceCharge->delete();
        return response()->json(['message' => 'Service charge deleted successfully']);
    }
}
