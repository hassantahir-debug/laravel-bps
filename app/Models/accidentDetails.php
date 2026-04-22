<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'patient_id',
    'date_of_accident',
    'place_of_accident',
    'time_of_accident',
    'veichle_no',
    'veichle_type',
    'veichle_model',
    'accident_description',
    'injury_description',
    'insurance_names',
    'insurance_policy_no',
])]
#[Table('accident_details')]
// Accident model
class AccidentDetails extends Model
{
    /** @use HasFactory<\Database\Factories\AccidentDetailsFactory> */
    use HasFactory;
    // Type casting
    protected function casts(): array
    {
        return [
            'patient_id' => 'integer',
            'date_of_accident' => 'date',
            'time_of_accident' => 'date',
            'place_of_accident' => 'string',
            'veichle_no' => 'string',
            'veichle_type' => 'string',
            'veichle_model' => 'string',
            'accident_description' => 'string',
            'injury_description' => 'string',
            'insurance_names' => 'string',
            'insurance_policy_no' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
    // Patient relation
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
