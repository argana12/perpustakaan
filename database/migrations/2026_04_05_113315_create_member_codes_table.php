<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            // type menentukan member_type user yang memakai kode ini
            $table->enum('type', ['student', 'teacher']);
            $table->boolean('is_used')->default(false);
            $table->foreignId('used_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('used_at')->nullable();
            // siapa petugas yang generate kode ini
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            // opsional: kode bisa expired
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_codes');
    }
};
