<x-mail::message>
# Appointment Confirmation

Dear {{ $appointment->patient->name }},

This is to inform you of your upcoming _{{ $appointment->appointmentType->name }}_ appointment. Please see the details below.

<x-mail::panel>
Appointment Scheduled For: {{ $appointment->start_date->format('F d, Y') }} at {{ $appointment->start_date->format('H:i A') }}
- Location: Limitless Regenerative
- Address: 6430 Richmond Ave. #150 Houston, TX 
</x-mail::panel>

If you have any questions or need to reschedule please contact us at: office@limitlessregenerative.com or 888 212 0632

---

_CONFIDENTIALITY NOTICE: The information enclosed with this transmission are the private, confidential property of the sender, and the material is privileged communication intended solely for the individual indicated. If you are not the intended recipient, you are notified that any review, disclosure, copying, distribution, or the taking of any other action relevant to the contents of this transmission are strictly prohibited. If you have received this transmission in error, please notify us immediately at 888 212 0632 or office@limitlessregenerative.com_
</x-mail::message>
