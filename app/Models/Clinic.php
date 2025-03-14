<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clinic extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'clinics';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'clinic_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address_line_1',
        'address_line_2',
        'city',
        'state_id',
        'zip_code',
        'price_read',
        'price_scan',
    ];

    /**
     * Get the clinic's address attribute
     */
    public function getAddressAttribute() {
        $address = $this->address_line_1;

        if ($this->address_line_2) {
            $address = "{$address}, $this->address_line_2";
        }

        $address = "$address, {$this->city}";
        $address = "$address, {$this->state->name}";
        $address = "$address, {$this->zip_code}";

        return $address;
    }

    /**
     * Get the referrals that the clinic has.
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referral_id', 'referral_id');
    }
    
    /**
     * Get the state that the clinic belongs to.
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }

    /**
     * Get the users that belong to the clinic.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'pivot_clinics_users', 'clinic_id', 'user_id')
            ->using(Pivot\ClinicUser::class);
    }
}
