<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table('patients')]
#[Table(key: 'id')]
#[Fillable([
    'firstName',
    'email',
    'lastName',
    'middleName',
    'phone',
    'mobile',
    'dateOfBirth',
    'gender',
    'address',
    'city',
    'state',
    'postalCode',
    'country',
    'emergencyContactName',
    'emergencyContactPhone'
])]
class patient extends Model
{
    public function getFullNameAttribute(): string
    {
        return collect([$this->first_name, $this->middle_name, $this->last_name])
            ->filter()
            ->implode(' ');
    }
}
