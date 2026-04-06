<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordOtp extends Model
{
    protected $fillable = [
        'user_id',
        'otp',
        'expired_at',
        'attempt',
        'next_allowed_at',
    ];

    protected $casts = [
        'expired_at'      => 'datetime',
        'next_allowed_at' => 'datetime',
    ];
}