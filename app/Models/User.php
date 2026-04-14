<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
        'role', 'phone', 'department', 'is_active', 'round_robin_order',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function assignedCandidates()
    {
        return $this->hasMany(Candidate::class, 'assigned_to');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public static function nextAssignee(): ?self
    {
        return static::where('role', 'hr_staff')
            ->orWhere('role', 'admin')
            ->where('is_active', true)
            ->orderBy('round_robin_order')
            ->first();
    }

    public static function rotateRoundRobin(): void
    {
        $staff = static::whereIn('role', ['hr_staff', 'admin'])
            ->where('is_active', true)
            ->orderBy('round_robin_order')
            ->get();

        if ($staff->isEmpty()) return;

        $first    = $staff->first();
        $maxOrder = $staff->max('round_robin_order');
        $first->update(['round_robin_order' => $maxOrder + 1]);
    }
}
