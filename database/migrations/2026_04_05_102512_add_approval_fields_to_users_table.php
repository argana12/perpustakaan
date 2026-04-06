<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Status alur pendaftaran
            $table->enum('status', [
                'register',
                'pending',
                'pending_admin',
                'approved',
                'active',
                'rejected',
            ])->default('register')->after('is_verified');

            // Apakah daftar sebagai member atau petugas
            $table->enum('register_as', ['member', 'petugas'])->default('member')->after('status');

            // Kode aktivasi yang di-generate petugas untuk member
            $table->string('registration_code')->nullable()->after('register_as');

            // Berapa kali salah input kode aktivasi
            $table->integer('code_attempt')->default(0)->after('registration_code');

            // Apakah kode aktivasi sudah dipakai
            $table->boolean('code_used')->default(false)->after('code_attempt');

            // Batas waktu pending (24 jam sejak OTP verified)
            $table->timestamp('pending_expired_at')->nullable()->after('code_used');

            // Siapa petugas/admin yang approve
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('pending_expired_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'status',
                'register_as',
                'registration_code',
                'code_attempt',
                'code_used',
                'pending_expired_at',
                'approved_by',
            ]);
        });
    }
};
