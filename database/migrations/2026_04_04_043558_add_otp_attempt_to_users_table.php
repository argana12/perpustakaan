<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('otp_attempt')->default(0)->after('otp_expired_at');
            $table->timestamp('otp_next_allowed_at')->nullable()->after('otp_attempt');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['otp_attempt', 'otp_next_allowed_at']);
        });
    }
};
