<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('bills')]
#[Fillable([
    'visit_id',
    'bill_number',
    'bill_date',
    'procedure_codes',
    'charges',
    'insurance_coverage',
    'bill_amount',
    'discount_amount',
    'tax_amount',
    'outstanding_amount',
    'paid_amount',
    'status',
    'generated_document_path',
    'notes',
    'due_date',
])]
// Bill model
class Bill extends Model
{

    use SoftDeletes;
    // Type casting
    protected function casts(): array
    {
        return [
            'bill_date' => 'date',
            'due_date' => 'date',
            'procedure_codes' => 'json',
            'charges' => 'decimal:2',
            'insurance_coverage' => 'decimal:2',
            'bill_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'outstanding_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'deleted_at' => 'datetime',
        ];
    }


    // Visit relation
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    // Payments relation
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Documents relation
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
