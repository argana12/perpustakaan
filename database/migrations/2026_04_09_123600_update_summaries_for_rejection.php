<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE summaries MODIFY COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");

        Schema::table('summaries', function (Blueprint $table) {
            if (!Schema::hasColumn('summaries', 'review_note')) {
                $table->string('review_note', 255)->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('summaries', function (Blueprint $table) {
            if (Schema::hasColumn('summaries', 'review_note')) {
                $table->dropColumn('review_note');
            }
        });

        DB::statement("ALTER TABLE summaries MODIFY COLUMN status ENUM('pending','approved') NOT NULL DEFAULT 'pending'");
    }
};
