<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Insurance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'status',
        'description',
        'opened_date',
        'closed_date',
    ];
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

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}