<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferralStatus extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'referral_statuses';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'referral_status_id';

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
     * Get the referrals that belong to the referral status.
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referral_status_id', 'referral_status_id');
    }
}
