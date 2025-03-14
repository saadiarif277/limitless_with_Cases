<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserType extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_types';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_type_id';

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
     * Get the users that belong to the user type.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'user_type_id', 'user_type_id');
    }
}
