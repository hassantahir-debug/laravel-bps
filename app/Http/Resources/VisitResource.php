<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->appointment->case->patient->id,
            "patient_name" => $this->appointment?->case?->patient?->full_name,
            "case_type"    => $this->appointment?->case?->case_type,
            "case_category" => $this->appointment->case->case_category,
            "is_accident" => $this->appointment->case->is_accident,
            "appointment_date" => $this->appointment->appointment_date,
            "appointment_time" => $this->appointment->appointment_time,
            "doctor_name"  => $this->appointment?->doctor_name,
            "diagnosis" => $this->diagnosis
        ];
    }
}
