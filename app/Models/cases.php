<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Cases model
class Cases extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cases';
    protected $primaryKey = 'id';
    protected $fillable = [
        'patient_id',
        'case_number',
        'case_type',
        'case_category',
        'priority',
        'status',
        'description',
        'opened_date',
        'closed_date',
        'reffering_doctor_id'
    ];

    // Patient relation
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    // Doctor relation
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reffering_doctor_id');
    }

    // Appointments relation
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'case_id');
    }
}
