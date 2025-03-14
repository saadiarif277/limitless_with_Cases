<?php

namespace Database\Seeders\Production;

use App\Models\DocumentCategory;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentCategoryIds = DocumentCategory::pluck('document_category_id')->toArray();
        $permissionNames = Permission::pluck('name')->toArray();

        $roles = collect([
            ['name' => 'System'],
            ['name' => 'Administrator'],
            ['name' => 'Attorney'],
            ['name' => 'Doctor'],
            ['name' => 'Funding Company'],
            ['name' => 'Office Manager'],
            ['name' => 'Patient'],
        ])->each(function ($role) use ($documentCategoryIds, $permissionNames) {
            $role = Role::firstOrCreate([
                'name' => $role['name'],
            ], array_merge($role, [
                // ...
            ]));

            $role->documentCategories()->syncWithoutDetaching($documentCategoryIds);

            if (in_array($role['name'], ['System', 'Administrator'])) {
                $role->syncPermissions($permissionNames);
            }
        });
    }
}
