<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cases extends Model
{
    use HasFactory;

    protected $primaryKey = 'case_id';
    protected $table = 'cases';

    protected $fillable = [
        'patient_id',
        'attorney_id',
        'piloting_physician_id',
        'policy_limit_info',
        'primary_referral_id',
        'referral_ids',
        'icd10_codes',
        'cpt_codes',
        'service_billed',
        'billing_type',
        'is_cms1500_generated',
        'case_won',
        'outcome',
        'reduction_accepted',
        'is_closed',
        'closed_at',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id', 'user_id');
    }

    public function attorney()
    {
        return $this->belongsTo(User::class, 'attorney_id', 'user_id');
    }

    public function pilotingPhysician()
    {
        return $this->belongsTo(User::class, 'piloting_physician_id', 'user_id');
    }

    public function primaryReferral()
    {
        return $this->belongsTo(Referral::class, 'primary_referral_id', 'referral_id');
    }

    /**
     * Get all referrals attached to this case.
     */
    public function referrals()
    {
        $primaryReferral = $this->primaryReferral;
        $additionalReferrals = Referral::whereIn('referral_id', explode(',', $this->referral_ids))->get();

        return collect([$primaryReferral])->merge($additionalReferrals)->filter();
    }

    /**
     * Determine the status of the case based on the status of its referrals.
     */
    public function getStatusAttribute()
    {
        $referrals = $this->referrals();

        if ($referrals->isEmpty()) {
            return 'Pending'; // No referrals, default to pending
        }

        // Check if all referrals are "Settled"
        $allSettled = $referrals->every(function ($referral) {
            return $referral->referralStatus->name === 'Settled';
        });

        return $allSettled ? 'Complete' : 'Pending';
    }
}
