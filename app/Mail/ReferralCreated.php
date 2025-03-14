<?php

namespace App\Mail;

use App\Models\Referral;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReferralCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The referral to create an email for.
     *
     * @var Referral
     */
    protected $referral;

    /**
     * Create a new message instance.
     */
    public function __construct(Referral $referral)
    {
        $referral->load([
            // ...
        ]);

        $this->referral = $referral;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Referral Created',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.referral.referral-created',
            with: [
                'referral' => $this->referral,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
