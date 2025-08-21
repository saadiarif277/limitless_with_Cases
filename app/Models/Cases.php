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
        
        // LOP Fields
        'lop_date',
        'lop_expiration_date',
        'lop_status',
        'lop_acknowledgment_received',
        'lop_acknowledgment_date',
        'lop_verification_status',
        'lop_verification_date',
        'lop_document',
        
        // Attorney/Law Firm Information
        'law_firm_name',
        'attorney_contact_person',
        'attorney_phone',
        'attorney_fax',
        'attorney_bar_number',
        'attorney_file_number',
        
        // Case/Litigation Information
        'case_number',
        'court_jurisdiction',
        'insurance_company_name',
        'insurance_claim_number',
        'accident_date',
        'accident_description',
        
        // Financial Information
        'estimated_case_value',
        'contingency_percentage',
        'current_medical_specials',
        'outstanding_balance',
        
        // Treatment Authorization
        'authorized_treatment_types',
        'treatment_limitations',
        'authorization_expiration_date',
    ];

    /**
     * Get the piloting physician associated with the case.
     */
    public function pilotingPhysician()
    {
        return $this->belongsTo(User::class, 'piloting_physician_id');
    }

    /**
     * Get the doctor associated with the case.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the patient associated with the case.
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the attorney associated with the case.
     */
    public function attorney()
    {
        return $this->belongsTo(User::class, 'attorney_id');
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
     * Get reduction requests for this case.
     */
    public function reductionRequests()
    {
        return $this->hasMany(ReductionRequest::class, 'case_id', 'case_id');
    }

    /**
     * Create reduction requests for all referrals in this case.
     */
    public function createReductionRequests()
    {
        if (!$this->reduction_amount || !$this->reduction_requested) {
            return;
        }

        $referrals = $this->referrals();
        
        foreach ($referrals as $referral) {
            if ($referral) {
                ReductionRequest::create([
                    'case_id' => $this->case_id,
                    'referral_id' => $referral->referral_id,
                    'amount' => $this->reduction_amount,
                    'referral_status' => 'pending',
                    'doctor_decision' => 'pending',
                    'notes' => $this->attorney_notes,
                ]);
            }
        }
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
