<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table('patients')]
#[Fillable([
    'first_name',
    'email',
    'last_name',
    'middle_name',
    'phone',
    'mobile',
    'date_of_birth',
    'gender',
    'address',
    'city',
    'state',
    'postal_code',
    'country',
    'emergency_contact_name',
    'emergency_contact_phone'
])]
// Patient model
class Patient extends Model
{
    protected $appends = ['full_name'];
    use HasFactory, SoftDeletes;
    // Accident relation
    public function accident_details(): BelongsTo
    {
        return $this->belongsTo(AccidentDetails::class);
    }

    // Cases relation
    public function cases(): HasMany
    {
        return $this->hasMany(Cases::class);
    }

    // Full name
    public function getFullNameAttribute(): string
    {
        $middle = $this->middle_name ? $this->middle_name . ' ' : '';
        return $this->first_name . ' ' . $middle . $this->last_name;
    }
}
