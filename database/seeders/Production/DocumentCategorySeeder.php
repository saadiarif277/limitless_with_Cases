<?php

namespace Database\Seeders\Production;

use App\Models\DocumentCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentCategories = collect([
            ['name' => 'Financial Documents'],
            ['name' => 'Medical Documents'],
        ])->each(function ($documentCategory) {
            DocumentCategory::firstOrCreate([
                'name' => $documentCategory['name'],
            ], array_merge($documentCategory, [
                // ...
            ]));
        });
    }
}
