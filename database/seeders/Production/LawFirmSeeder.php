<?php

namespace Database\Seeders\Production;

use App\Models\LawFirm;
use App\Models\User;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class LawFirmSeeder extends Seeder
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

        // Create some basic law firms
        $lawFirms = collect([
            [
                'name' => 'Justice & Associates Law Firm',
                'email' => 'info@justiceassociates.com',
                'phone_number' => '(555) 111-2222',
                'address_line_1' => '100 Legal Plaza',
                'address_line_2' => 'Suite 200',
                'city' => 'Los Angeles',
                'state_id' => $states->first()->state_id,
                'zip_code' => '90210',
            ],
            [
                'name' => 'Legal Solutions Group',
                'email' => 'contact@legalsolutions.com',
                'phone_number' => '(555) 222-3333',
                'address_line_1' => '200 Court Street',
                'city' => 'New York',
                'state_id' => $states->count() > 1 ? $states[1]->state_id : $states->first()->state_id,
                'zip_code' => '10001',
            ],
            [
                'name' => 'Premier Legal Services',
                'email' => 'info@premierlegal.com',
                'phone_number' => '(555) 333-4444',
                'address_line_1' => '300 Attorney Blvd',
                'address_line_2' => 'Building C',
                'city' => 'Chicago',
                'state_id' => $states->count() > 2 ? $states[2]->state_id : $states->first()->state_id,
                'zip_code' => '60601',
            ],
        ])->each(function ($lawFirmData) {
            LawFirm::firstOrCreate([
                'email' => $lawFirmData['email'],
            ], $lawFirmData);
        });

        // Associate attorneys with law firms
        $attorneyRole = Role::where('name', 'Attorney')->first();
        if ($attorneyRole) {
            $attorneys = User::whereHas('roles', function($query) use ($attorneyRole) {
                $query->where('name', 'Attorney');
            })->get();

            $lawFirms = LawFirm::all();

            foreach ($attorneys as $index => $attorney) {
                // Assign each attorney to a law firm (cycling through available law firms)
                $lawFirm = $lawFirms[$index % $lawFirms->count()];
                $attorney->update(['law_firm_id' => $lawFirm->law_firm_id]);
                
                $this->command->info("Associated attorney {$attorney->name} with law firm {$lawFirm->name}");
            }
        }

        $this->command->info('LawFirmSeeder completed successfully.');
    }
}
