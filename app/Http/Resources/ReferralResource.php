<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferralResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'referral_date' => $this->referral_date->format('Y-m-d'),
            'injury_date' => $this->injury_date->format('Y-m-d'),
            'appointment_start_date' => $this->appointment
                ? $this->appointment->start_date->format('m-d-Y h:i A')
                : null,
        ]);
    }
}
