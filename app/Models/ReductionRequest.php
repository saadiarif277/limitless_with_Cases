<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cases;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Referral;

class ReductionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'referral_id',
        'amount',
        'file_path',
        'referral_status', // Status of the referral
        'doctor_decision', // Doctor's decision (accepted, rejected, pending)
        'counter_offer', // Doctor's counter offer
        'notes', // Doctor's notes
    ];

    // Relationships
    public function case()
    {
        return $this->belongsTo(Cases::class, 'case_id');
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class, 'referral_id', 'referral_id');
    }
}
