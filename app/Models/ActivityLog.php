<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'loggable_id', 'loggable_type', 'user_id',
        'action', 'old_value', 'new_value', 'description',
    ];

    public function loggable() { return $this->morphTo(); }
    public function user()     { return $this->belongsTo(User::class); }
}
