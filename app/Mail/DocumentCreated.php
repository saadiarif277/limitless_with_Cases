<?php

namespace App\Mail;

use App\Models\Document;
use App\Models\Referral;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The document to create an email for.
     *
     * @var Referral
     */
    protected $document;

    /**
     * The referral to create an email for.
     *
     * @var Referral
     */
    protected $referral;

    /**
     * Create a new message instance.
     */
    public function __construct(Document $document, Referral $referral)
    {
        $document->load(['documentType']);

        $this->document = $document;
        $this->referral = $referral;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Document Created',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.document.document-created',
            with: [
                'document' => $this->document,
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
