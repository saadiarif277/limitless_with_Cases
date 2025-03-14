<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Jobs\Document\SanitizeDocumentFile;
use App\Models\Document;

class SanitizeDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sanitize-documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sanitizes the documents table and related files.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sanitizing Model: "App\Models\Document".');

        $documentFiles = Storage::files('public/uploads/documents');
        $documentFileChunks = array_chunk($documentFiles, 100);

        foreach ($documentFileChunks as $documentFileChunk) {
            foreach ($documentFileChunk as $documentFile) {
                $this->info("Verifying: '{$documentFile}'.");
                SanitizeDocumentFile::dispatch($documentFile);
            }
        }

        $this->info('Model "App\Models\Document" has been sanitized.');
    }
}
