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
        Schema::dropIfExists('member_codes');
        Schema::dropIfExists('staff_codes');

        Schema::create('activation_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role', ['student','teacher','petugas']);
            $table->boolean('is_used')->default(false);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['register_as', 'registration_code', 'code_used']);
            $table->string('work_days')->nullable()->after('is_verified');
            $table->boolean('is_visible')->default(true)->after('work_days');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activation_codes');

        Schema::table('users', function (Blueprint $table) {
            $table->enum('register_as', ['member', 'petugas'])->default('member')->after('status');
            $table->string('registration_code')->nullable()->after('register_as');
            $table->boolean('code_used')->default(false)->after('code_attempt');
            $table->dropColumn(['work_days', 'is_visible', 'approved_at']);
        });

        // recreate old tables just in case (partial definition based on basic needs)
        Schema::create('staff_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->boolean('is_used')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('used_at')->nullable();
            $table->foreignId('used_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('member_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['student', 'teacher']);
            $table->boolean('is_used')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('used_at')->nullable();
            $table->foreignId('used_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }
};
