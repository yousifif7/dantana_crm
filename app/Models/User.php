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
        'employee_id', 'first_name', 'last_name', 'email', 'phone',
        'password', 'role_id', 'department_id', 'reports_to',
        'hire_date', 'age', 'is_active', 'two_factor_enabled'
    ];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'hire_date' => 'date',
        'is_active' => 'boolean',
        'two_factor_enabled' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'reports_to');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'reports_to');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function assignedProcesses()
    {
        return $this->hasMany(Process::class, 'assigned_to');
    }

    public function hasPermission(string $permission, string $level = 'view'): bool
    {
        return $this->role->permissions()
            ->where('name', $permission)
            ->where('access_level', $level)
            ->exists();
    }

    public function canApprove(): bool
    {
        return in_array($this->role->name, ['chairman', 'md', 'general_manager', 'department_head']);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
