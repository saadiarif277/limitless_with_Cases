<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLogResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use \Spatie\Activitylog\Models\Activity as ActivityLog;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('panel/admin/activity-log/activity-log-list', [
            'activity-log' => ActivityLogResource::collection(
                ActivityLog::query()
                    ->when($request->filled('searchQuery'), function ($query) use ($request) {
                        $searchTerm = strtolower($request->get('searchQuery'));
                        $query->orWhereRaw('LOWER(description) LIKE ?', ["%{$searchTerm}%"]);
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(20)
                ),
        ]);
    }
}
