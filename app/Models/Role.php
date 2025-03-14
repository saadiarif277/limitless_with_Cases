<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    /**
     * Get the document categories that belong to the role.
     */
    public function documentCategories()
    {
        return $this->belongsToMany(DocumentCategory::class, 'pivot_document_categories_roles', 'role_id', 'document_category_id')
            ->using(Pivot\DocumentCategoryRole::class);
    }
}
