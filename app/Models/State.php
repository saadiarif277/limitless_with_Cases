<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'states';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'state_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the clinics that belong to the state.
     */
    public function clinics()
    {
        return $this->hasMany(Clinic::class, 'state_id', 'state_id');
    }

    /**
     * Get the users that belong to the state.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the document types that belong to the state.
     */
    public function documentTypes()
    {
        return $this->belongsToMany(DocumentType::class, 'pivot_document_types_states', 'state_id', 'document_type_id')
            ->using(Pivot\DocumentTypeState::class);
    }
}
