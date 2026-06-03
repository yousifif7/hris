<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'documentable_id', 'documentable_type', 'name', 'type',
        'file_path', 'mime_type', 'file_size', 'uploaded_by',
    ];

    protected function casts(): array
    {
        return ['file_size' => 'integer'];
    }

    public function documentable() { return $this->morphTo(); }
    public function uploadedBy()   { return $this->belongsTo(User::class, 'uploaded_by'); }
}
