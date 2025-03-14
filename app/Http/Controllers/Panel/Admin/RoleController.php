<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Models\DocumentCategory;
use App\Models\Permission;
use App\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentCategoryResource;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Http\Requests\DestroyRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('panel/admin/roles/roles-list', [
            'roles' => RoleResource::collection(
                Role::query()
                    ->when($request->filled('searchQuery'), function ($query) use ($request) {
                        $searchTerm = strtolower($request->get('searchQuery'));
                        $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"]);
                    })
                    ->withCount('users')
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
        return Inertia::render('panel/admin/roles/roles-create-edit', [
            'documentCategories' => DocumentCategoryResource::collection(DocumentCategory::all()),
            'permissions' => PermissionResource::collection(Permission::all()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->get('name'),
        ]);

        $role->syncPermissions($request->get('permission_ids'));
        $role->documentCategories()->sync($request->get('document_category_ids'));

        return to_route('panel.admin.roles.show', [
            'role' => $role->getKey(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Role $role): Response
    {
        $role->load([
            'documentCategories',
            'permissions',
        ]);

        return Inertia::render('panel/admin/roles/roles-create-edit', [
            'documentCategories' => DocumentCategoryResource::collection(DocumentCategory::all()),
            'permissions' => PermissionResource::collection(Permission::all()),
            'role' => new RoleResource($role),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update([
            'name' => $request->get('name'),
        ]);

        $role->syncPermissions($request->get('permission_ids'));
        $role->documentCategories()->sync($request->get('document_category_ids'));

        return to_route('panel.admin.roles.show', [
            'role' => $role->getKey(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyRoleRequest $request, Role $role)
    {
        $role->delete();
        return to_route('panel.admin.roles.index');
    }
}
