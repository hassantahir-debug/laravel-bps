<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'visit_id',
    'bill_number',
    'bill_date',
    'procedures_codes',
    'charges',
    'insurance_coverage',
    'bill_amount',
    'discount_amount',
    'tax_amount',
    'paid_amount',
    'outstanding_amount',
    'status',
    'generated_document_path',
    'notes',
    'due_date'
])]
class bill extends Model
{
    use HasFactory, SoftDeletes;

    public function visit()
    {
        return $this->belongsTo(visits::class);
    }

}
