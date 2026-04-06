<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivationCode extends Model
{
    protected $fillable = [
        'code',
        'user_id',
        'role',
        'is_used',
        'created_by',
        'used_at',
        'expired_at',
    ];

    protected $casts = [
        'is_used'    => 'boolean',
        'used_at'    => 'datetime',
        'expired_at' => 'datetime',
    ];

    /**
     * User yang berhak menggunakan kode ini.
     */
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Petugas/Admin yang generate kode ini.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Cek apakah kode masih bisa dipakai (belum digunakan & belum expired).
     */
    public function isValid(): bool
    {
        if ($this->is_used) return false;
        if ($this->expired_at && now()->gt($this->expired_at)) return false;
        return true;
    }

    /**
     * Label role yang lebih human-readable.
     */
    public function roleLabel(): string
    {
        return match ($this->role) {
            'student' => 'Murid',
            'teacher' => 'Guru',
            'petugas' => 'Petugas',
            default   => ucfirst($this->role),
        };
    }
}
