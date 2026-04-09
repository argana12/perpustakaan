<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookQueueManager
{
    public function __construct(
        protected BookQueueDeadlineCalculator $deadlineCalculator,
    ) {
    }

    public function markNextQueueReady(Book $book, ?Carbon $readyAt = null): ?BookQueue
    {
        $readyAt ??= now();

        return DB::transaction(function () use ($book, $readyAt) {
            $queue = BookQueue::query()
                ->where('book_id', $book->id)
                ->where('status', BookQueue::STATUS_WAITING)
                ->orderBy('created_at')
                ->lockForUpdate()
                ->first();

            if (!$queue) {
                return null;
            }

            $queue->update([
                'status' => BookQueue::STATUS_READY,
                'ready_at' => $readyAt,
            ]);

            return $queue->fresh();
        });
    }

    public function callQueue(BookQueue $queue, ?Carbon $calledAt = null): BookQueue
    {
        $calledAt ??= now();

        $queue->update([
            'status' => BookQueue::STATUS_CALLED,
            'called_at' => $calledAt,
            'deadline' => $this->deadlineCalculator->calculate($calledAt),
        ]);

        return $queue->fresh();
    }

    public function autoCallReadyQueues(?Carbon $now = null): Collection
    {
        $now ??= now();
        $threshold = $now->copy()->subMinutes((int) config('library_queue.auto_call_after_minutes', 60));

        return BookQueue::query()
            ->where('status', BookQueue::STATUS_READY)
            ->whereNotNull('ready_at')
            ->where('ready_at', '<=', $threshold)
            ->orderBy('ready_at')
            ->get()
            ->map(fn (BookQueue $queue) => $this->callQueue($queue, $now));
    }

    public function expireOverdueQueues(?Carbon $now = null): Collection
    {
        $now ??= now();

        return BookQueue::query()
            ->where('status', BookQueue::STATUS_CALLED)
            ->whereNotNull('deadline')
            ->where('deadline', '<=', $now)
            ->orderBy('deadline')
            ->get()
            ->map(function (BookQueue $queue) use ($now) {
                $queue->update([
                    'status' => BookQueue::STATUS_EXPIRED,
                ]);

                return $queue->fresh();
            });
    }
}
