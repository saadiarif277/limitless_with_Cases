<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'created_at' => isset($this->created_at) ? $this->created_at->format('h:i:s A T') : null,
            'start_date' => isset($this->start_date) ? $this->start_date->format('Y-m-d H:i:s') : null,
            'end_date' => isset($this->end_date) ? $this->end_date->format('Y-m-d H:i:s') : null,
        ]);
    }
}
