<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CaseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'case_id' => $this->case_id,
            'patient_name' => $this->patient_name,
            'attorney_name' => $this->attorney_name,
            'piloting_physician' => $this->piloting_physician,
            'bill_type' => $this->bill_type,
            'status' => $this->status,
        ];
    }
}
