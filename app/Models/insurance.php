<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Insurance model
class Insurance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'city',
        'status',
    ];
    // Type casting
    protected function casts(): array
    {
        return [
            'opened_date' => 'date',
            'closed_date' => 'date',
            'status' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }


    // Active scope
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}