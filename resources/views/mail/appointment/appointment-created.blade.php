<x-mail::message>
# Appointment Confirmation

Dear {{ $appointment->patient->name }},

This is to inform you of your upcoming _{{ $appointment->appointmentType->name }}_ appointment. Please see the details below.

<x-mail::panel>
Appointment Scheduled For: {{ $appointment->start_date->format('F d, Y') }} at {{ $appointment->start_date->format('H:i A') }}
- Location: {{ $appointment->clinic->name }}
- Address: {{ $appointment->clinic->address }}
</x-mail::panel>

For any inquiries or to reschedule, please contact us at {{ $appointment->clinic->phone_number }}.

---

_CONFIDENTIALITY NOTICE: This message is intended exclusively for the individual or entity to which it is addressed and may contain confidential and/or privileged information. Any unauthorized review, use, disclosure, or distribution is prohibited. If you are not the intended recipient, please contact the sender by reply email and destroy all copies of the original message._
</x-mail::message>
