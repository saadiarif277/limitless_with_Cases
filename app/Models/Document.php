<?php

namespace App\Models;

use App\Jobs\Document\SanitizeDocumentFile;
use App\Jobs\Document\DispatchDocumentCreatedEmails;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'documents';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'document_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'document_type_id',
        'path',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($document) {
            DispatchDocumentCreatedEmails::dispatch($document)->delay(now()->addMinutes(1));
        });

        static::updated(function ($document) {
            // Handle file deletion on update
            if ($document->wasChanged('path')) {
                $oldPath = $document->getOriginal('path');
                // Dispatch the job to sanitize the old file
                SanitizeDocumentFile::dispatch($oldPath);
                DispatchDocumentCreatedEmails::dispatch($document)->delay(now()->addMinutes(1));
            }
        });

        static::deleted(function ($document) {
            // Handle file deletion on hard delete
            if (!$document->trashed()) { // Check if it's not a soft delete
                // Dispatch the job to sanitize the file associated with the deleted document
                SanitizeDocumentFile::dispatch($document->path);
            }
        });
    }

    /**
     * Dispatch emails 
     */

    /**
     * Get the document type that the document belongs to.
     */
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id', 'document_type_id');
    }

    /**
     * Get the referrals that belong to the document.
     */
    public function referrals()
    {
        return $this->belongsToMany(Referral::class, 'pivot_documents_referrals', 'document_id', 'referral_id')
            ->using(Pivot\DocumentReferral::class);
    }
}
