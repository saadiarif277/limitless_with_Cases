<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modelFiles = File::allFiles(app_path('Models'));

        $models = collect($modelFiles)->map(function ($file) {
            return Str::before($file->getFilename(), '.php');
        });

        $models->each(function ($model) {
            $modelActions = ['list', 'show', 'store', 'update', 'destroy'];
            $modelName = Str::slug(Str::kebab($model)); // The slugified name of the model.

            foreach($modelActions as $modelAction) {
                $permissionName = "{$modelName}.${modelAction}";
                $permission = [
                    'name' => $permissionName,
                ];

                Permission::firstOrCreate($permission, array_merge($permission, [
                    // ...
                ]));
            }
        });

        /**
         * Generate Secondary Permissions.
         */
        $permissions = collect([
            'app.panel.admin',
        ])->each(function ($permission) {
            Permission::firstOrCreate([
                'name' => $permission,
            ], [
                // ...
            ]);
        });
    }
}
