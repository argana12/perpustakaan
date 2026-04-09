<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('book_queues')) {
            return;
        }

        Schema::table('book_queues', function (Blueprint $table) {
            if (!Schema::hasColumn('book_queues', 'notified_at')) {
                $table->timestamp('notified_at')->nullable()->after('deadline');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('book_queues') || !Schema::hasColumn('book_queues', 'notified_at')) {
            return;
        }

        Schema::table('book_queues', function (Blueprint $table) {
            $table->dropColumn('notified_at');
        });
    }
};

