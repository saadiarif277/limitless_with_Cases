<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferralReason extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'referral_reasons';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'referral_reason_id';

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
     * Get the referrals that belong to the referral reason.
     */
    public function referrals()
    {
        return $this->belongsToMany(Referral::class, 'pivot_referrals_referral_reasons', 'referral_reason_id', 'referral_id')
            ->using(Pivot\ReferralReferralReason::class);
    }
}
