<!DOCTYPE html>
<html>
    <head>
        <title>Referral Document</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $appCssFiles = $manifest['resources/js/app.js']['css'] ?? [];
        @endphp

        @foreach ($appCssFiles as $cssFile)
            <link rel="stylesheet" href="{{ public_path('build/' . $cssFile) }}">
        @endforeach

        <style>
            .text-color-primary {
                color: #1363DF;
            }

            .text-color-default {
                color: #1e293b;
            }

            .text-color-muted {
                color: #6b7280;
            }
        </style>
    </head>

    <body class="font-sans">
        <div class="bg-gray-50 py-4" style="width: 100%; text-align: center;">
            <img
                src="{{ public_path('assets/image/logo.svg') }}"
                style="display: block; height: 80px; margin: 0 auto;"
            />
        </div>

        <div class="px-6 py-4">
            <h1 class="text-color-default text-lg">
                INVOICE
            </h1>

            <div>
                <p class="text-color-default">Statement Date: {{ now()->format('F d, Y') }}</p>
                <p class="text-color-default">Due Date: {{ now()->format('F d, Y') }}</p>
            </div>
        </div>

        <div class="py-4">
            <table style="width: 100%;">
                <tbody>
                    <tr>
                        <td class="px-6" style="width: 50%; vertical-align: top">
                            <h2 class="text-sm text-color-primary uppercase tracking-wide">Bill To</h2>

                            <div>
                                <h4 class="text-color-default">{{ $referral->patientUser->name }}</h4>

                                <p class="italic text-color-muted">
                                    {{ $referral->patientUser->address_line_1 }},
                                    @if($referral->patientUser->address_line_2)
                                        {{ $referral->patientUser->address_line_2 }},
                                    @endif
                                    {{ $referral->patientUser->city }},
                                    {{ $referral->patientUser->state->name }},
                                    {{ $referral->patientUser->zip_code }}
                                </p>
                            </div>
                        </td>

                        <td class="px-6" style="width: 50%; vertical-align: top">
                            <h2 class="text-sm text-color-primary uppercase tracking-wide">Bill From</h2>
                            <div>
                                <h4 class="text-color-default">Limitless Regenerative, LLC</h4>

                                <p class="italic text-color-muted">
                                    5900 Balcones Drive, STE 100, Austin, Texas, 78731
                                </p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="py-4">
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th class="bg-gray-50 text-xs text-color-muted px-6 py-4 font-normal uppercase text-left">Client</th>
                        <th class="bg-gray-50 text-xs text-color-muted px-6 py-4 font-normal uppercase text-right">Visit Number</th>
                        <th class="bg-gray-50 text-xs text-color-muted px-6 py-4 font-normal uppercase text-left">Doctor</th>
                        <th class="bg-gray-50 text-xs text-color-muted px-6 py-4 font-normal uppercase text-left">Patient</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-top: 1px solid lightgray">
                        <td class="text-color-default text-sm px-6 py-4">Limitless Regenerative, LLC</td>
                        <td class="text-color-default text-sm px-6 py-4" align="right">1</td>
                        <td class="text-color-default text-sm px-6 py-4">{{ $referral->doctorUser->name }}</td>
                        <td class="text-color-default text-sm px-6 py-4">
                            {{ $referral->patientUser->name }}
                            <div class="text-color-muted italic">
                                {{ $referral->patientUser->birthdate->format('F d, Y') }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="py-4">
            <div class="bg-gray-50 py-3 text-left px-6" style="border-bottom: 2px solid lightgray;">
                <h2 class="text-sm text-color-primary uppercase tracking-wide">Breakdown of Charges</h2>
            </div>
            
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th class="bg-gray-50 text-xs text-color-muted px-6 py-4 font-normal uppercase text-left">Date of Service</th>
                        <th class="bg-gray-50 text-xs text-color-muted px-6 py-4 font-normal uppercase text-left">Description</th>
                        <th class="bg-gray-50 text-xs text-color-muted px-6 py-4 font-normal uppercase text-left">ICD10 Code</th>
                        <th class="bg-gray-50 text-xs text-color-muted px-6 py-4 font-normal uppercase text-right">Units</th>
                        <th class="bg-gray-50 text-xs text-color-muted px-6 py-4 font-normal uppercase text-right">Charge per Unit</th>
                        <th class="bg-gray-50 text-xs text-color-muted px-6 py-4 font-normal uppercase text-right">Charge Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-top: 1px solid lightgray">
                        <td class="text-color-default text-sm px-6 py-4">{{ now()->format('F d, Y') }}</td>
                        <td class="text-color-default text-sm px-6 py-4">Neural Scan</td>
                        <td class="text-color-default text-sm px-6 py-4">&nbsp;</td>
                        <td class="text-color-default text-sm px-6 py-4" align="right">1</td>
                        <td class="text-color-default text-sm px-6 py-4" align="right">$7,500.00</td>
                        <td class="text-color-default text-sm px-6 py-4" align="right">$7,500.00</td>
                    </tr>
                    <tr style="border-top: 1px solid lightgray">
                        <td class="text-color-default text-sm px-6 py-4">{{ now()->format('F d, Y') }}</td>
                        <td class="text-color-default text-sm px-6 py-4">Neural Scan Read</td>
                        <td class="text-color-default text-sm px-6 py-4">&nbsp;</td>
                        <td class="text-color-default text-sm px-6 py-4" align="right">1</td>
                        <td class="text-color-default text-sm px-6 py-4" align="right">$500.00</td>
                        <td class="text-color-default text-sm px-6 py-4" align="right">$500.00</td>
                    </tr>
                    <tr style="border-top: 1px solid lightgray">
                        <td class="text-color-default text-sm px-6 py-4 font-bold" align="right" colspan="100%">$8,000.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>