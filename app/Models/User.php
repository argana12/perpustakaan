<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'member_type',
        'otp',
        'otp_expired_at',
        'otp_attempt',
        'otp_next_allowed_at',
        'otp_unlocked',
        'is_verified',
        // Approval system fields
        'status',
        'code_attempt',
        'pending_expired_at',
        'approved_by',
        'approved_at',
        'work_days',
        'is_visible',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'otp_expired_at'      => 'datetime',
            'otp_next_allowed_at' => 'datetime',
            'pending_expired_at'  => 'datetime',
            'approved_at'         => 'datetime',
            'password'            => 'hashed',
            'is_verified'         => 'boolean',
            'otp_unlocked'        => 'boolean',
            'is_visible'          => 'boolean',
        ];
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function activationCode()
    {
        return $this->hasOne(ActivationCode::class, 'user_id');
    }
}