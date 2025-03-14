<x-mail::message>
# Referral Notification

Hello!

A new referral has been created with the reference #{{ str_pad($referral->getKey(), 5, "0", STR_PAD_LEFT) }}. You can view more details by logging into the application <a href="{{ config('app.url') }}">here</a>.

If the link above doesn't work, please copy and paste the URL found below into your browser: <br /> 

<div>
    <em>{{ config('app.url') }}</em>
</div>

</x-mail::message>
