<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 000
            Production\AppointmentTypeSeeder::class,
            Production\DocumentCategorySeeder::class,
            Production\PermissionSeeder::class,
            Production\ReferralReasonSeeder::class,
            Production\ReferralStatusSeeder::class,
            
            // 100
            Production\DocumentTypeSeeder::class,
            Production\RoleSeeder::class,

            // 200
            Production\StateSeeder::class,
            
            // 300
            Production\UserSeeder::class,

            // 400
            Production\MedicalSpecialtySeeder::class,
            Production\LawFirmSeeder::class,
            Production\ClinicSeeder::class,
        ]);

        if (!app()->environment(['production', 'staging'])) {
            \App\Models\LawFirm::factory(5)->create();
            \App\Models\User::factory(100)->create();
            \App\Models\Clinic::factory(8)->create();
            // \App\Models\Appointment::factory(50)->create();
        }
    }
}
