<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The appointment to create an email for.
     *
     * @var Referral
     */
    protected $appointment;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment)
    {
        $appointment->load([
            'appointmentType',
            'clinic.state',
            'patient',
        ]);

        $this->appointment = $appointment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Appointment Created',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $stateView = 'mail.appointment.appointment-created-' . strtolower($this->appointment->clinic->state->name);
        $view = \Illuminate\Support\Facades\View::exists($stateView) ? $stateView : 'mail.appointment.appointment-created';

        return new Content(
            markdown: $view,
            with: [
                'appointment' => $this->appointment,
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
