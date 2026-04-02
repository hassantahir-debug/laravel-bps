<?php

namespace App\Models;

use Illuminate\Console\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('payments')]
#[Table(key: 'id')]
#[Fillable([
    'bill_id',
    'payment_number',
    'amount_paid',
    'payment_mode',
    'check_number',
    'bank_name',
    'transaction_reference',
    'payment_date',
    'payment_status',
    'cheque_file_path',
    'notes',
    'received_by',
])]
class Payment extends Model
{
    use HasFactory, SoftDeletes;


    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
