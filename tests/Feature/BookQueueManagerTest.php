<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookQueue;
use App\Models\User;
use App\Services\BookQueueManager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BookQueueManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropAllTables();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('whatsapp_number')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('isbn')->nullable();
            $table->string('status')->default('available');
            $table->timestamps();
        });

        Schema::create('book_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default(BookQueue::STATUS_WAITING);
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->timestamps();
        });

        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('description');
            $table->timestamps();
        });
    }

    public function test_it_marks_the_oldest_waiting_queue_as_ready(): void
    {
        $book = Book::query()->create([
            'title' => 'Algoritma',
            'author' => 'Admin',
            'isbn' => '123',
            'status' => 'available',
        ]);

        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $firstQueue = BookQueue::query()->create([
            'user_id' => $firstUser->id,
            'book_id' => $book->id,
            'status' => BookQueue::STATUS_WAITING,
            'created_at' => Carbon::parse('2026-04-08 08:00:00'),
            'updated_at' => Carbon::parse('2026-04-08 08:00:00'),
        ]);

        BookQueue::query()->create([
            'user_id' => $secondUser->id,
            'book_id' => $book->id,
            'status' => BookQueue::STATUS_WAITING,
            'created_at' => Carbon::parse('2026-04-08 09:00:00'),
            'updated_at' => Carbon::parse('2026-04-08 09:00:00'),
        ]);

        $queue = app(BookQueueManager::class)->markNextQueueReady(
            $book,
            Carbon::parse('2026-04-08 10:00:00'),
        );

        $this->assertNotNull($queue);
        $this->assertTrue($queue->is($firstQueue->fresh()));
        $this->assertSame(BookQueue::STATUS_READY, $queue->status);
        $this->assertSame('2026-04-08 10:00:00', $queue->ready_at?->format('Y-m-d H:i:s'));
    }

    public function test_it_auto_calls_ready_queues_after_one_hour(): void
    {
        $book = Book::query()->create([
            'title' => 'Basis Data',
            'author' => 'Admin',
            'isbn' => '456',
            'status' => 'available',
        ]);

        $user = User::factory()->create();

        $queue = BookQueue::query()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => BookQueue::STATUS_READY,
            'ready_at' => Carbon::parse('2026-04-08 08:00:00'),
        ]);

        app(BookQueueManager::class)->autoCallReadyQueues(
            Carbon::parse('2026-04-08 09:00:00'),
        );

        $queue->refresh();

        $this->assertSame(BookQueue::STATUS_CALLED, $queue->status);
        $this->assertSame('2026-04-08 09:00:00', $queue->called_at?->format('Y-m-d H:i:s'));
        $this->assertSame('2026-04-09 08:00:00', $queue->deadline?->format('Y-m-d H:i:s'));
    }

    public function test_it_expires_called_queues_when_deadline_passes(): void
    {
        $book = Book::query()->create([
            'title' => 'Jaringan',
            'author' => 'Admin',
            'isbn' => '789',
            'status' => 'available',
        ]);

        $user = User::factory()->create();

        $queue = BookQueue::query()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => BookQueue::STATUS_CALLED,
            'called_at' => Carbon::parse('2026-04-08 09:00:00'),
            'deadline' => Carbon::parse('2026-04-09 08:00:00'),
        ]);

        app(BookQueueManager::class)->expireOverdueQueues(
            Carbon::parse('2026-04-09 08:00:00'),
        );

        $queue->refresh();

        $this->assertSame(BookQueue::STATUS_EXPIRED, $queue->status);
    }
}
