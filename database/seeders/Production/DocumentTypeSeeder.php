<?php

namespace Database\Seeders\Production;

use App\Models\DocumentCategory;
use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypes = collect([
            ['name' => 'Referral Summary',     'document_category_id' => DocumentCategory::MEDICAL,   'is_permanent' => FALSE, 'is_generated' => TRUE],
            ['name' => 'Invoice',              'document_category_id' => DocumentCategory::FINANCIAL, 'is_permanent' => FALSE, 'is_generated' => TRUE],
            ['name' => 'Letter of Protection', 'document_category_id' => DocumentCategory::FINANCIAL, 'is_permanent' => TRUE,  'is_generated' => FALSE],
            ['name' => 'Medical Results',      'document_category_id' => DocumentCategory::MEDICAL,   'is_permanent' => FALSE, 'is_generated' => FALSE],
            ['name' => 'Medical Lien',         'document_category_id' => DocumentCategory::FINANCIAL, 'is_permanent' => FALSE, 'is_generated' => FALSE],
        ])->each(function ($documentType) {
            DocumentType::firstOrCreate([
                'name' => $documentType['name'],
            ], array_merge($documentType, [
                // ...
            ]));
        });
    }
}
