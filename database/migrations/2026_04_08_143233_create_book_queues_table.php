<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('books')) {
            Schema::create('books', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('author')->nullable();
                $table->string('isbn')->nullable();
                $table->enum('status', ['available', 'borrowed'])->default('available');
                $table->timestamps();
            });
        }

        Schema::dropIfExists('book_queues');
        Schema::create('book_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['waiting', 'ready', 'called', 'expired', 'done', 'cancelled'])->default('waiting');
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_queues');
    }
};
