<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'bill_id'               => $this->bill_id,
            'payment_number'        => $this->payment_number,
            'amount_paid'           => $this->amount_paid,
            'payment_mode'          => $this->payment_mode,
            'check_number'          => $this->check_number,
            'bank_name'             => $this->bank_name,
            'transaction_reference' => $this->transaction_reference,
            'payment_date'          => $this->payment_date,
            'payment_status'        => $this->payment_status,
            'cheque_file_path'      => $this->cheque_file_path,
            'notes'                 => $this->notes,
            'received_by'           => $this->received_by,
            'created_at'            => $this->created_at,
            'bill'                  => new BillResource($this->whenLoaded('bill')),
        ];
    }
}
