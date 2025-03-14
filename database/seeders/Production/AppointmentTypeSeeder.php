<?php

namespace Database\Seeders\Production;

use App\Models\AppointmentCategory;
use App\Models\AppointmentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $appointmentTypes = collect([
            ['name' => 'Neural Scan', 'color' => 'green'],
            ['name' => 'Follow Up', 'color' => 'orange'],
        ])->each(function ($appointmentType) {
            AppointmentType::firstOrCreate([
                'name' => $appointmentType['name'],
            ], array_merge($appointmentType, [
                // ...
            ]));
        });
    }
}
