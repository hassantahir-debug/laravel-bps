<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'case_id',
    'appointment_type',
    'appointment_status',
    'appointment_date',
    'appointment_time',
    'notes',
    'doctor_name',
    'doctor_id',
    'duration_minutes',
    'specialty_required',
    'reminder_sent'
])]
#[Table('appointments')]
// Appointment model
class Appointment extends Model
{
    use SoftDeletes, HasFactory;

    // Case relation
    public function case()
    {
        return $this->belongsTo(Cases::class, 'case_id');
    }

    // Doctor relation
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // Visit relation
    public function visit()
    {
        return $this->hasOne(Visit::class, 'appointment_id');
    }
}
