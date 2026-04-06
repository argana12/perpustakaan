<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffCode extends Model
{
    protected $fillable = [
        'code',
        'is_used',
        'used_at',
        'used_by',
        'created_by',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usedByUser()
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
