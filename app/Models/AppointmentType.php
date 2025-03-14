<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentType extends Model
{
    use HasFactory;
    use SoftDeletes;

    const LOP = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'appointment_types';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'appointment_type_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'color',
    ];

    /**
     * Get the appointments that belong to the appointment type.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'appointment_type_id', 'appointment_type_id');
    }
}
