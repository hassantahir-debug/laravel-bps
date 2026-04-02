<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'appointment_id',
    'visit_date',
    'visit_time',
    'diagnosis',
    'treatment_notes',
    'prescription',
    'follow_up_required',
    'follow_up_date',
    'visit_status'
])]
#[Table('visits')]
class visits extends Model
{
    use HasFactory, SoftDeletes;
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

}
