<?php

namespace App\Http\Controllers\Panel\Admin;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Models\IcdCode;
use Illuminate\Http\Request;
use App\Http\Resources\IcdCodeResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\IcdCodeImport;
use Illuminate\Support\Facades\Validator;
use Inertia\Response;


class Icd10CodeController extends Controller
{



public function index(Request $request): Response
{
    return Inertia::render('panel/admin/ictcodes/ictcode-list', [
        'ictCodes' => IcdCodeResource::collection(
            IcdCode::query()
                ->when($request->filled('searchQuery'), function ($query) use ($request) {
                    $searchTerm = strtolower($request->get('searchQuery'));
                    $query->whereRaw('LOWER(code) LIKE ?', ["%{$searchTerm}%"]);
                })
                ->orderBy('code')
                ->paginate(10)
        ),
    ]);
}



public function store(Request $request): Response
{
    // Validate input
    $validated = $request->validate([
        'code' => 'required|string|max:10|unique:icd10_codes,code',
        'description' => 'required|string|max:255',
    ]);

    // Create ICT Code
    $ictCode = IcdCode::create($validated);

    // Return Inertia response with success message
    return Inertia::render('panel/admin/ictcodes/ictcode-list', [
        'message' => 'ICT Code created successfully.',
        'ictCodes' => IcdCode::all(),
    ]);
}


public function bulkUpload(Request $request)
{
    // Validate the uploaded file
    $validator = Validator::make($request->all(), [
        'file' => 'required|file|mimes:csv',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    try {
        Excel::import(new IcdCodeImport, $request->file('file'));

        return redirect()->back()->with('success', 'ICT Codes uploaded successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'ICT Codes Failed to Upload');
    }
}

    public function create(){
        return Inertia::render('panel/admin/ictcodes/ictcode-create');
    }

    public function show($id)
    {
        $icd10Code = Icd10Code::findOrFail($id);
        return response()->json($icd10Code);
    }

    public function update(Request $request, $id)
    {
        $icd10Code = Icd10Code::findOrFail($id);
        $request->validate([
            'code' => 'required|unique:icd10_codes,code,' . $id . '|max:10',
            'description' => 'required|string|max:255',
        ]);

        $icd10Code->update($request->all());
        return response()->json($icd10Code);
    }

    public function destroy($id)
    {
        $icd10Code = Icd10Code::findOrFail($id);
        $icd10Code->delete();
        return response()->json(['message' => 'ICD-10 code deleted successfully']);
    }
}
