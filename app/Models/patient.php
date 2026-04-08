<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
class patient extends Model
{
    protected $appends = ['full_name'];
    use HasFactory, SoftDeletes;

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->middle_name . '' . $this->last_name;
    }
}
