<?php

namespace Database\Seeders\Production;

use App\Models\MedicalSpecialty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicalSpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicalSpecialties = collect([
            ['name' => 'Chiropractor'],
            ['name' => 'Neurologist'],
            ['name' => 'Internal Medicine'],
            ['name' => 'Orthopedic Surgeons'],
            ['name' => 'Physical Therapist'],
            ['name' => 'Cardiologist'],
            ['name' => 'Plastic Surgeon'],
            ['name' => 'Psychiatrist'],
        ])->each(function ($medicalSpecialty) {
            MedicalSpecialty::firstOrCreate([
                'name' => $medicalSpecialty['name'],
            ], array_merge($medicalSpecialty, [
                // ...
            ]));
        });
    }
}
