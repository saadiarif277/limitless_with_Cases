<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Mail;

class Appointment extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'appointments';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'appointment_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'appointment_type_id',
        'clinic_id',
        'patient_user_id',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($appointment) {
            Mail::to($appointment->patient->email)->send(new \App\Mail\AppointmentCreated($appointment));
        });
    }


    /**
     * Get the appointment type that the appointment belongs to.
     */
    public function appointmentType()
    {
        return $this->belongsTo(AppointmentType::class, 'appointment_type_id', 'appointment_type_id');
    }

    /**
     * Get the clinic that the appointment belongs to.
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'clinic_id');
    }

    /**
     * Get the patient that the appointment belongs to.
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_user_id', 'user_id');
    }

    /**
     * Get the referral that the appointment belongs to.
     */
    public function referral()
    {
        return $this->belongsTo(Referral::class, 'appointment_id', 'appointment_id');
    }
}
