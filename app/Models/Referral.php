<?php

namespace App\Models;

use App\Jobs\Referral\GenerateReferralDocument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Mail;
use App\Models\reductionRequest;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Referral extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'referrals';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'referral_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'appointment_id',
        'attorney_user_id',
        'clinic_id',
        'doctor_user_id',
        'injury_date',
        'patient_user_id',
        'referral_date',
        'referral_status_id',
        'source_user_id',
        'state_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'referral_date' => 'datetime',
        'injury_date' => 'datetime',
    ];

    /**
     * The "boot" method of the model.
     *
     * Here we define the model's event hooks, particularly
     * the action to take when a new Referral model instance is created.
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * Register a created model event with the dispatcher.
         *
         * @param \App\Models\Referral $referral The newly created referral instance.
         * @return void
         */
        static::created(function ($referral) {
            GenerateReferralDocument::dispatch($referral)->delay(now()->addMinutes(1));
            Mail::to("office@limitlessregenerative.com")->send(new \App\Mail\ReferralCreated($referral));
        });

        static::updated(function ($referral) {
            GenerateReferralDocument::dispatch($referral)->delay(now()->addMinutes(1));
        });
    }

    /**
     * Get the appointment that the referral belongs to.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }

    /**
     * Get the attorney user that the referral belongs to.
     */
    public function attorneyUser()
    {
        return $this->belongsTo(User::class, 'attorney_user_id', 'user_id');
    }

    /**
     * Get the clinic that the referral belongs to.
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'clinic_id');
    }

    /**
     * Get the doctor user that the referral belongs to.
     */
    public function doctorUser()
    {
        return $this->belongsTo(User::class, 'doctor_user_id', 'user_id');
    }

    /**
     * Get the documents that belong to the referral.
     */
    public function documents()
    {
        return $this->belongsToMany(Document::class, 'pivot_documents_referrals', 'referral_id', 'document_id')
            ->using(Pivot\DocumentReferral::class);
    }

    /**
     * Get the patient user that the referral belongs to.
     */
    public function patientUser()
    {
        return $this->belongsTo(User::class, 'patient_user_id', 'user_id');
    }

    /**
     * Get the referral status that the referral belongs to.
     */
    public function referralStatus()
    {
        return $this->belongsTo(ReferralStatus::class, 'referral_status_id', 'referral_status_id');
    }

    /**
     * Get the documents that belong to the referral.
     */
    public function referralReasons()
    {
        return $this->belongsToMany(ReferralReason::class, 'pivot_referrals_referral_reasons', 'referral_id', 'referral_reason_id')
            ->using(Pivot\ReferralReferralReason::class);
    }

    /**
     * Get the source user that the referral belongs to.
     */
    public function sourceUser()
    {
        return $this->belongsTo(User::class, 'source_user_id', 'user_id');
    }

    /**
     * Get the state that the referral belongs to.
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }

    public function reductionRequests(): HasMany
    {
        return $this->hasMany(ReductionRequest::class, 'referral_id', 'referral_id');
    }
}
