<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'description'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public static function isHoliday(Carbon $date): bool
    {
        return static::query()
            ->whereDate('date', $date->toDateString())
            ->exists();
    }
}
