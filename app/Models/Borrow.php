<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Borrow extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_LATE = 'late';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_LOST = 'lost';

    protected $fillable = [
        'user_id',
        'book_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'fine',
        'fine_paid_at',
    ];

    protected function casts(): array
    {
        return [
            'borrow_date' => 'datetime',
            'due_date' => 'datetime',
            'return_date' => 'datetime',
            'fine_paid_at' => 'datetime',
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

    public function summary(): HasOne
    {
        return $this->hasOne(Summary::class);
    }
}
