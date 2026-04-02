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
class appointment extends Model
{
    use SoftDeletes, HasFactory;
    public function case()
    {
        return $this->belongsTo(cases::class, 'case_id');
    }

    public function doctor()
    {
        return $this->belongsTo(user::class, 'doctor_id');
    }
}
