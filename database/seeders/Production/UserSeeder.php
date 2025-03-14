<?php

namespace Database\Seeders\Production;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domain = parse_url(config('app.url'), PHP_URL_HOST);

        $users = collect([
            [
                'userData' => [
                    'name' => 'System User',
                    'email' => 'system@'.$domain,
                    'password' => bcrypt('password'),
                ],
                'roleName' => 'System', // Role name
            ],
            [
                'userData' => [
                    'name' => 'Administrator User',
                    'email' => 'admin@'.$domain,
                    'password' => bcrypt('password'),
                ],
                'roleName' => 'Administrator', // Role name
            ],
            [
                'userData' => [
                    'name' => 'Attorney User',
                    'email' => 'attorney@'.$domain,
                    'password' => bcrypt('password'),
                ],
                'roleName' => 'Attorney', // Role name
            ],
            [
                'userData' => [
                    'name' => 'Doctor User',
                    'email' => 'doctor@'.$domain,
                    'password' => bcrypt('password'),
                ],
                'roleName' => 'Doctor', // Role name
            ],
            [
                'userData' => [
                    'name' => 'Funding Company User',
                    'email' => 'funding.company@'.$domain,
                    'password' => bcrypt('password'),
                ],
                'roleName' => 'Funding Company', // Role name
            ],
            [
                'userData' => [
                    'name' => 'Office Manager User',
                    'email' => 'office.manager@'.$domain,
                    'password' => bcrypt('password'),
                ],
                'roleName' => 'Office Manager', // Role name
            ],
            [
                'userData' => [
                    'name' => 'Patient User',
                    'email' => 'patient@'.$domain,
                    'password' => bcrypt('password'),
                ],
                'roleName' => 'Patient', // Role name
            ],
        ])->each(function ($item) {
            $user = User::firstOrCreate([
                'email' => $item['userData']['email'],
            ], $item['userData']);
        
            if ($user->wasRecentlyCreated) {
                $role = Role::where('name', $item['roleName'])->first();
        
                if ($role) {
                    $user->assignRole($role);
                }
            }
        });
    }
}
