<?php

namespace App\Jobs\Document;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SanitizeDocumentFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The file path to check against the Document models.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new job instance.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Include trashed (soft deleted) models in the search
        $document = Document::withTrashed()->where('path', $this->path)->first();

        if (!$document) {
            // No document found - delete the file
            if (Storage::exists($this->path)) {
                Storage::delete($this->path);
                logger()->info("No document found for the file, deleted: {$this->path}");
            }
        } else {
            logger()->info("Document exists for: {$this->path}, [{$document->getKey()}]");
        }
    }
}
