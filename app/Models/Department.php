<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'code', 'description', 'general_manager_id',
        'department_head_id', 'is_active',
        // extended fields
        'address', 'phone', 'contact_email', 'city', 'state', 'postal_code', 'country', 'extra_info'
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function generalManager()
    {
        return $this->belongsTo(User::class, 'general_manager_id');
    }

    public function departmentHead()
    {
        return $this->belongsTo(User::class, 'department_head_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
