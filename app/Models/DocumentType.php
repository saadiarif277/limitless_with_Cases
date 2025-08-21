<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
    use HasFactory;
    use SoftDeletes;

    const REFERRAL_SUMMARY = 1;
    const INVOICE = 2;
    const LETTER_OF_PROTECTION = 3;
    const MEDICAL_RESULTS = 4;
    const MEDICAL_LIEN = 5;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'document_types';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'document_type_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'document_category_id',
        'is_generated',
    ];

    /**
     * The "boot" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($documentType) {
            if ($documentType->is_generated) {
                abort(403, 'You are not allowed to delete a generated document type.');
            }
        });
    }

    /**
     * Get the documents that belong to the document type.
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'document_type_id', 'document_type_id');
    }

    /**
     * Get the document category that the document type belongs to.
     */
    public function documentCategory()
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id', 'document_category_id');
    }

    /**
     * Get the locations that belong to the document type.
     */
    public function states()
    {
        return $this->belongsToMany(State::class, 'pivot_document_types_states', 'document_type_id', 'state_id')
            ->using(Pivot\DocumentTypeState::class);
    }
}
