<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('password_otps', function (Blueprint $table) {
            $table->integer('attempt')->default(1)->after('expired_at');
            $table->timestamp('next_allowed_at')->nullable()->after('attempt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('password_otps', function (Blueprint $table) {
            $table->dropColumn(['attempt', 'next_allowed_at']);
        });
    }
};

