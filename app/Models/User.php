<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    const SYSTEM = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'birthdate',
        'email',
        'password',
        'address_line_1',
        'address_line_2',
        'city',
        'zip_code',
        'gender',
        'phone_number',
        'state_id',
        'height',
        'weight',
        'law_firm_id',
        'medical_specialty_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthdate' => 'date',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function primaryClinic()
    {
        return $this->hasOneThrough(
            Clinic::class,  // The model you want to access.
            Pivot\ClinicUser::class,  // The intermediate/pivot model.
            'user_id',  // Foreign key on the pivot model.
            'clinic_id',  // Local key on the final model (Clinic).
            'user_id',  // Local key on the local model (User).
            'clinic_id'  // Foreign key on the final model.
        )->where('pivot_clinics_users.is_primary', true);
    }

    /**
     * Get the clinics that belong to the user.
     */
    public function clinics()
    {
        return $this->belongsToMany(Clinic::class, 'pivot_clinics_users', 'user_id', 'clinic_id')
            ->using(Pivot\ClinicUser::class);
    }

    /**
     * Get the medical specialty that the user belongs to.
     */
    public function medicalSpecialty()
    {
        return $this->belongsTo(MedicalSpecialty::class, 'medical_specialty_id', 'medical_specialty_id');
    }

    /**
     * Get the state that the user belongs to.
     */
    public function lawFirm()
    {
        return $this->belongsTo(LawFirm::class, 'law_firm_id', 'law_firm_id');
    }

    /**
     * Get the referrals that the user belongs to.
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }

    /**
     * Get the referrals that belong to the user.
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'patient_user_id', 'user_id');
    }
}
