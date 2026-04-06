<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberCode extends Model
{
    protected $fillable = [
        'code',
        'type',
        'is_used',
        'used_by',
        'used_at',
        'created_by',
        'expired_at',
    ];

    protected $casts = [
        'is_used'    => 'boolean',
        'used_at'    => 'datetime',
        'expired_at' => 'datetime',
    ];

    /**
     * Petugas yang generate kode ini.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User yang memakai kode ini.
     */
    public function usedByUser()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    /**
     * Cek apakah kode masih bisa dipakai.
     */
    public function isValid(): bool
    {
        if ($this->is_used) return false;
        if ($this->expired_at && now()->gt($this->expired_at)) return false;
        return true;
    }

    /**
     * Label tipe member yang mudah dibaca.
     */
    public function typeLabel(): string
    {
        return match ($this->type) {
            'student' => 'Murid',
            'teacher' => 'Guru',
            default   => ucfirst($this->type),
        };
    }
}
