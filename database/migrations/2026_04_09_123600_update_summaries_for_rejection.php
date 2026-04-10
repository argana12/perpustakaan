<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('summaries', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
        });

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

        Schema::table('summaries', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved'])->default('pending')->change();
        });
    }
};
