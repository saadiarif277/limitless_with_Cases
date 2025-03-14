<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    const FINANCIAL = 1;
    const MEDICAL   = 2;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'document_categories';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'document_category_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the documents that belong to the document category.
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'document_category_id', 'document_category_id');
    }

    /**
     * Get the document types that belong to the document category.
     */
    public function documentTypes()
    {
        return $this->hasMany(DocumentType::class, 'document_category_id', 'document_category_id');
    }

    /**
     * Get the roles that belong to the document category.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'pivot_document_categories_roles', 'document_category_id', 'role_id')
            ->using(Pivot\DocumentCategoryRole::class);
    }
}
