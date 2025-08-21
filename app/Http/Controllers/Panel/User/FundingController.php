<?php

namespace App\Http\Controllers\Panel\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FundingController extends Controller
{
    /**
     * Display the funding request form.
     */
    public function request(): Response
    {
        // Check if user is a doctor
        if (!auth()->user()->hasRole('Doctor')) {
            abort(403, 'Only doctors can access this page.');
        }

        return Inertia::render('panel/user/funding/request-funding');
    }

    /**
     * Store a new funding request.
     */
    public function store(Request $request)
    {
        // Check if user is a doctor
        if (!auth()->user()->hasRole('Doctor')) {
            abort(403, 'Only doctors can submit funding requests.');
        }

        $validated = $request->validate([
            'doctorName' => 'required|string|max:255',
            'licenseNumber' => 'required|string|max:255',
            'specialty' => 'required|string|max:255',
            'yearsOfPractice' => 'required|integer|min:0|max:50',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'practiceName' => 'required|string|max:255',
            'practiceType' => 'required|string|max:255',
            'practiceAddress' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zipCode' => 'required|string|max:20',
            'patientsPerMonth' => 'nullable|integer|min:0',
            'fundingPurpose' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'timeline' => 'required|string|max:255',
            'expectedROI' => 'nullable|string|max:500',
            'declaration' => 'required|accepted',
        ]);

        // Here you would typically:
        // 1. Store the funding request in the database
        // 2. Send notification to PROVE company
        // 3. Handle file uploads if any
        // 4. Send confirmation email to the doctor

        // For now, we'll just return a success response
        return response()->json([
            'success' => true,
            'message' => 'Funding request submitted successfully!'
        ]);
    }
}
