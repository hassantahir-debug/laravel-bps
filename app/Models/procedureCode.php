<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// CPT codes
class ProcedureCode extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'procedures_codes';
    protected $fillable = [
        'code',
        'description',
        'price'
    ];
    protected $dates = ['deleted_at'];
}
