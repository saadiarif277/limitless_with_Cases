<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class SanitizeRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sanitize-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sanitizes the data for the roles table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sanitizing Model: "Spatie\Permission\Models\Role".');

        Role::query()->chunk(100, function($roles) {
            foreach ($roles as $role) {
                $this->info("Sanitizing: {$role->name}");

                $role->update([
                    'name' => Str::title($role->name),
                ]);
            }
        });

        $this->info('Model "Spatie\Permission\Models\Role" has been sanitized.');
    }
}
