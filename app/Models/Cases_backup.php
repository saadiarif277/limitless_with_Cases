<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cases_backup extends Model
{
    use HasFactory;

    protected $primaryKey = 'case_id';
    protected $table = 'cases';


    protected $fillable = [
        'patient_id',             // Patient associated with the case
        'attorney_id',            // Attorney managing the case
        'piloting_physician_id',  // Physician associated with the case
        'policy_limit_info',      // Insurance policy information
        'primary_referral_id',    // Primary referral ID
        'referral_ids',           // Other referral IDs (likely a comma-separated list or JSON)
        'icd10_codes',            // ICD-10 diagnosis codes (likely a comma-separated list)
        'cpt_codes',              // CPT procedure codes (likely a comma-separated list)
        'service_billed',         // Amount billed for the service
        'billing_type',           // Type of billing
        'is_cms1500_generated',   // Indicates if CMS-1500 is generated
        'case_won',               // Indicates if the case is won
        'outcome',                // Outcome of the case
        'reduction_accepted',     // Indicates if reduction was accepted
        'is_closed',              // Indicates if the case is closed
        'closed_at',              // Timestamp when the case was closed
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id','user_id');
    }

    public function attorney()
    {
        return $this->belongsTo(User::class, 'attorney_id','user_id');
    }

    public function pilotingPhysician()
    {
        return $this->belongsTo(User::class, 'piloting_physician_id','user_id');
    }

    public function primaryReferral()
    {
        return $this->belongsTo(Referral::class, 'primary_referral_id');
    }
}
