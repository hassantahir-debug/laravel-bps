<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'visit_id'               => $this->visit_id,
            'bill_number'            => $this->bill_number,
            'bill_date'              => $this->bill_date,
            'procedure_codes'        => $this->procedure_codes,
            'charges'                => $this->charges,
            'insurance_coverage'     => $this->insurance_coverage,
            'bill_amount'            => $this->bill_amount,
            'discount_amount'        => $this->discount_amount,
            'tax_amount'             => $this->tax_amount,
            'outstanding_amount'     => $this->outstanding_amount,
            'paid_amount'            => $this->paid_amount,
            'status'                 => $this->status,
            'generated_document_path' => $this->generated_document_path,
            'notes'                  => $this->notes,
            'due_date'               => $this->due_date,
            'created_at'             => $this->created_at,
            'visit'                  => new VisitResource($this->whenLoaded('visit')),
            'payments'               => PaymentResource::collection($this->whenLoaded('payments')),
            // 'documents'              => DocumentResource::collection($this->whenLoaded('documents')),
        ];
    }
}
