<?php

namespace App\Services;

use App\Models\BookQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookQueueNotificationService
{
    public function notifyCalled(BookQueue $queue, string $source = 'auto'): void
    {
        if ($queue->notified_at) {
            return;
        }

        $queue->loadMissing(['user', 'book']);

        $user = $queue->user;
        $book = $queue->book;

        if (!$user || !$book) {
            return;
        }

        $deadlineText = optional($queue->deadline)->format('d-m-Y H:i') ?? '-';
        $message = "Halo {$user->name}, buku {$book->title} ({$book->code}) siap diambil. Batas waktu: {$deadlineText}.";

        // Placeholder integrasi WA gratis:
        // saat ini dicatat ke log agar alur tidak gagal bila gateway belum siap.
        Log::info('Book queue WhatsApp notification', [
            'queue_id' => $queue->id,
            'source' => $source,
            'whatsapp_number' => $user->whatsapp_number,
            'message' => $message,
        ]);

        if (!empty($user->email)) {
            Mail::raw($message, function ($mail) use ($user) {
                $mail->to($user->email)
                    ->subject('Notifikasi Antrean Buku - Siap Diambil');
            });
        }

        $queue->update([
            'notified_at' => now(),
        ]);
    }
}

