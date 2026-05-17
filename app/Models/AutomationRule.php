<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    protected $fillable = [
        'trigger_event', 'trigger_value', 'action_type',
        'action_config', 'delay_hours', 'is_active',
    ];

    protected function casts(): array
    {
        return ['action_config' => 'array', 'is_active' => 'boolean'];
    }
}
