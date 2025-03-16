<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReductionRequest;

class ReductionRequestController extends Controller
{
    //

    public function store(Request $request)
{
    $request->validate([
        'case_id' => 'required|exists:cases,case_id',
        'referral_id' => 'required|exists:referrals,referral_id',
        'amount' => 'required|numeric|min:0',
        'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        'referral_status' => 'required|in:won,lost,pending,reduction_request_sent', // Validate referral_status
    ]);

    // Store the uploaded file
    $filePath = null;
    if ($request->hasFile('file')) {
        $filePath = $request->file('file')->store('reduction_files', 'public');
    }

    // Create the reduction request
    $reductionRequest = ReductionRequest::create([
        'case_id' => $request->case_id,
        'referral_id' => $request->referral_id,
        'amount' => $request->amount,
        'file_path' => $filePath,
        'referral_status' => $request->referral_status, // Set referral_status
        'doctor_decision' => 'pending', // Default doctor decision
        'counter_offer' => null, // Default counter offer
    ]);

    return response()->json([
        'message' => 'Reduction request created successfully',
        'data' => $reductionRequest,
    ], 201);
}
public function update(Request $request, $id)
{
    $request->validate([
        'doctor_decision' => 'nullable|in:accepted,rejected,pending', // Validate doctor's decision
        'counter_offer' => 'nullable|numeric|min:0', // Validate counter offer
    ]);

    $reductionRequest = ReductionRequest::findOrFail($id);

    // Update the doctor's decision and counter offer
    $reductionRequest->update([
        'doctor_decision' => $request->doctor_decision ?? $reductionRequest->doctor_decision,
        'counter_offer' => $request->counter_offer ?? $reductionRequest->counter_offer,
    ]);

    return response()->json([
        'message' => 'Reduction request updated successfully',
        'data' => $reductionRequest,
    ]);
}
}
