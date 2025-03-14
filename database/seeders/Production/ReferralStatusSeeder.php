<?php

namespace Database\Seeders\Production;

use App\Models\ReferralStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReferralStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $referralStatuses = collect([
            ['name' => 'Draft',     'order' => 10],
            ['name' => 'Pending',   'order' => 20],
            ['name' => 'Booked',    'order' => 30],
            ['name' => 'Test Done', 'order' => 40],
            ['name' => 'Signed',    'order' => 50],
            ['name' => 'Submitted', 'order' => 60],
            ['name' => 'Settled',   'order' => 70],
        ])->each(function ($referralStatus) {
            ReferralStatus::updateOrCreate([
                'name' => $referralStatus['name'],
            ], array_merge($referralStatus, [
                // ...
            ]));
        });
    }
}
