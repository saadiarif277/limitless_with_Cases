<?php

namespace Database\Seeders\Production;

use App\Models\Clinic;
use App\Models\User;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first few states
        $states = State::take(5)->get();
        
        if ($states->isEmpty()) {
            $this->command->warn('No states found. Please run StateSeeder first.');
            return;
        }

        // Create some basic clinics
        $clinics = collect([
            [
                'name' => 'Limitless Regeneration Clinic',
                'email' => 'info@limitlessregen.com',
                'phone_number' => '(555) 123-4567',
                'address_line_1' => '123 Medical Center Dr',
                'address_line_2' => 'Suite 100',
                'city' => 'Los Angeles',
                'state_id' => $states->first()->state_id,
                'zip_code' => '90210',
                'price_read' => '500',
                'price_scan' => '300',
            ],
            [
                'name' => 'Advanced Medical Solutions',
                'email' => 'contact@advancedmedical.com',
                'phone_number' => '(555) 234-5678',
                'address_line_1' => '456 Health Plaza',
                'city' => 'New York',
                'state_id' => $states->count() > 1 ? $states[1]->state_id : $states->first()->state_id,
                'zip_code' => '10001',
                'price_read' => '600',
                'price_scan' => '350',
            ],
            [
                'name' => 'Premier Healthcare Clinic',
                'email' => 'info@premierhealth.com',
                'phone_number' => '(555) 345-6789',
                'address_line_1' => '789 Wellness Blvd',
                'address_line_2' => 'Building A',
                'city' => 'Chicago',
                'state_id' => $states->count() > 2 ? $states[2]->state_id : $states->first()->state_id,
                'zip_code' => '60601',
                'price_read' => '550',
                'price_scan' => '325',
            ],
        ])->each(function ($clinicData) {
            Clinic::firstOrCreate([
                'email' => $clinicData['email'],
            ], $clinicData);
        });

        // Associate doctors with clinics
        $doctorRole = Role::where('name', 'Doctor')->first();
        if ($doctorRole) {
            $doctors = User::whereHas('roles', function($query) use ($doctorRole) {
                $query->where('name', 'Doctor');
            })->get();

            $clinics = Clinic::all();

            foreach ($doctors as $index => $doctor) {
                // Assign each doctor to a clinic (cycling through available clinics)
                $clinic = $clinics[$index % $clinics->count()];
                $doctor->clinics()->syncWithoutDetaching([$clinic->clinic_id]);
                
                $this->command->info("Associated doctor {$doctor->name} with clinic {$clinic->name}");
            }
        }

        $this->command->info('ClinicSeeder completed successfully.');
    }
}
