<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookQueue extends Model
{
    use HasFactory;

    public const STATUS_WAITING = 'waiting';
    public const STATUS_READY = 'ready';
    public const STATUS_CALLED = 'called';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_DONE = 'done';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'ready_at',
        'called_at',
        'deadline',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'ready_at' => 'datetime',
            'called_at' => 'datetime',
            'deadline' => 'datetime',
            'notified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
