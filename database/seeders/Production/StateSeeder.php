<?php

namespace Database\Seeders\Production;

use App\Models\State;
use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Document Types
        $documentTypeIds = DocumentType::pluck('document_type_id')->toArray();

        // States
        $states = collect([
            ['name' => 'Alabama'],
            ['name' => 'Alaska'],
            ['name' => 'Arizona'],
            ['name' => 'Arkansas'],
            ['name' => 'California'],
            ['name' => 'Colorado'],
            ['name' => 'Connecticut'],
            ['name' => 'Delaware'],
            ['name' => 'Florida'],
            ['name' => 'Georgia'],
            ['name' => 'Hawaii'],
            ['name' => 'Idaho'],
            ['name' => 'Illinois'],
            ['name' => 'Indiana'],
            ['name' => 'Iowa'],
            ['name' => 'Kansas'],
            ['name' => 'Kentucky'],
            ['name' => 'Louisiana'],
            ['name' => 'Maine'],
            ['name' => 'Maryland'],
            ['name' => 'Massachusetts'],
            ['name' => 'Michigan'],
            ['name' => 'Minnesota'],
            ['name' => 'Mississippi'],
            ['name' => 'Missouri'],
            ['name' => 'Montana'],
            ['name' => 'Nebraska'],
            ['name' => 'Nevada'],
            ['name' => 'New Hampshire'],
            ['name' => 'New Jersey'],
            ['name' => 'New Mexico'],
            ['name' => 'New York'],
            ['name' => 'North Carolina'],
            ['name' => 'North Dakota'],
            ['name' => 'Ohio'],
            ['name' => 'Oklahoma'],
            ['name' => 'Oregon'],
            ['name' => 'Pennsylvania'],
            ['name' => 'Rhode Island'],
            ['name' => 'South Carolina'],
            ['name' => 'South Dakota'],
            ['name' => 'Tennessee'],
            ['name' => 'Texas'],
            ['name' => 'Utah'],
            ['name' => 'Vermont'],
            ['name' => 'Virginia'],
            ['name' => 'Washington'],
            ['name' => 'West Virginia'],
            ['name' => 'Wisconsin'],
            ['name' => 'Wyoming'],
        ])->each(function ($state) use ($documentTypeIds) {
            $state = State::firstOrCreate([
                'name' => $state['name'],
            ], array_merge($state, [
                // ... Additional fields if needed
            ]));

            $state->documentTypes()->sync($documentTypeIds);
        });
    }
}
