<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'bill_id',
    'document_type',
    'file_name',
    'file_path',
    'file_type',
    'file_size',
    'upload_date',
    'uploaded_by',
    'version'
])]
// Document model
class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'documents';

    // Bill relation
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    // Uploader relation
    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Size formatter
    public function getReadableFileSizeAttribute()
    {
        $size = $this->file_size;
        if ($size >= 1073741824) {
            return number_format($size / 1073741824, 2) . ' GB';
        } elseif ($size >= 1048576) {
            return number_format($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            return number_format($size / 1024, 2) . ' KB';
        } else {
            return $size . ' bytes';
        }
    }
}
