<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingTemplate extends Model
{
    protected $fillable = ['name', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function tasks()
    {
        return $this->hasMany(OnboardingTask::class, 'template_id');
    }
}
