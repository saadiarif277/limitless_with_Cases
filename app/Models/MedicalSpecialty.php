<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalSpecialty extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'medical_specialties';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'medical_specialty_id';

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
     * Get the users that belong to the medical specialty.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'medical_specialty_id', 'medical_specialty_id');
    }
}
