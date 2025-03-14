<?php

namespace App\Http\Controllers\Panel\Admin;
use App\Http\Controllers\Controller;
use App\Models\CptCode;
use Illuminate\Http\Request;
use App\Http\Resources\CptCodeResource;
use Inertia\Inertia;
use Inertia\Response;

class CptCodeController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('panel/admin/cptcodes/cptcode-list', [
            'CptCodes' => CptCodeResource::collection(
                CptCode::query()
                    ->when($request->filled('searchQuery'), function ($query) use ($request) {
                        $searchTerm = strtolower($request->get('searchQuery'));
                        $query->whereRaw('LOWER(code) LIKE ?', ["%{$searchTerm}%"]);
                    })
                    ->orderBy('code')
                    ->paginate(10)
            ),
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
        return Inertia::render('panel/admin/cptcodes/cptcode-create');
    }

    public function store(Request $request) : Response
    {
        $request->validate([
            'code' => 'required|unique:cpt_codes|max:10',
            'description' => 'required|string|max:255',
            'default_value' => 'required|numeric',
        ]);

        $cptCode = CptCode::create($request->all());
        return Inertia::render('panel/admin/cptcodes/cptcode-list', [
            'message' => 'Cpt Code created successfully.',
            'CptCodes' => CptCode::all(),
        ]);
    }

    public function show($id)
    {
        $cptCode = CptCode::findOrFail($id);
        return response()->json($cptCode);
    }

    public function update(Request $request, $id)
    {
        $cptCode = CptCode::findOrFail($id);
        $request->validate([
            'code' => 'required|unique:cpt_codes,code,' . $id . '|max:10',
            'description' => 'required|string|max:255',
            'fee' => 'required|numeric',
        ]);

        $cptCode->update($request->all());
        return response()->json($cptCode);
    }

    public function destroy($id)
    {
        $cptCode = CptCode::findOrFail($id);
        $cptCode->delete();
        return response()->json(['message' => 'CPT code deleted successfully']);
    }
}
