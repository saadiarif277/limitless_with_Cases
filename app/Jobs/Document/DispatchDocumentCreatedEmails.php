<?php

namespace App\Jobs\Document;

use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class DispatchDocumentCreatedEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $document;

    /**
     * Create a new job instance.
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $referrals = $this->document->referrals()->get();

        $referrals->each(function ($referral) {
            $recipients = [];

            if ($this->document->document_type_id == DocumentType::MEDICAL_RESULTS) {
                if ($referral->sourceUser) {
                    $recipients[] = $referral->sourceUser->email;
                }

                if ($referral->attorneyUser && $referral->attorneyUser->lawFirm) {
                    $recipients[] = $referral->attorneyUser->lawFirm->email;
                }
            }

            if ($this->document->document_type_id == DocumentType::MEDICAL_LIEN) {
                $recipients[] = "office@limitlessregenerative.com";
            }

            if ($this->document->document_type_id == DocumentType::LETTER_OF_PROTECTION) {
                $recipients[] = "office@limitlessregenerative.com";
            }

            foreach ($recipients as $recipient) {
                Mail::to($recipient)->send(new \App\Mail\DocumentCreated($this->document, $referral));
            }
        });
    }
}
