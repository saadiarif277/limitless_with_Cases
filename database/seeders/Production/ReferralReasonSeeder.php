<?php

namespace Database\Seeders\Production;

use App\Models\ReferralReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReferralReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $referralReasons = collect([
            ['name' => 'Headaches/Migraines'],
            ['name' => 'Memory and/or Concentration Problems'],
            ['name' => 'Inability to Focus/Attention Problems'],
            ['name' => 'Blurry/Double Vision'],
            ['name' => 'Depression'],
            ['name' => 'Personality Changes'],
            ['name' => 'Brain Bleed/Swelling'],
            ['name' => 'PTSD'],
            ['name' => 'Sensitivity to Light or Noise'],
            ['name' => 'Dizziness/Balance Problems/Ringing in Ears'],
            ['name' => 'Alteration of Speech/Abnormal Speech'],
            ['name' => 'Mental Fogginess'],
            ['name' => 'Anxiety Disorder'],
            ['name' => 'Mood Swings'],
            ['name' => 'Abnormal CT/MRI of Brain'],
            ['name' => 'Sluggishness/Lethargy.Fatigue'],
            ['name' => 'Neck Pain'],
            ['name' => 'Other'],
        ])->each(function ($referralReason) {
            ReferralReason::firstOrCreate([
                'name' => $referralReason['name'],
            ], array_merge($referralReason, [
                // Additional fields here if needed
            ]));
        });
    }
}
