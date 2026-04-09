<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            if (!Schema::hasColumn('borrows', 'fine_paid_at')) {
                $table->dateTime('fine_paid_at')->nullable()->after('fine');
            }
        });
    }

    public function down(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            if (Schema::hasColumn('borrows', 'fine_paid_at')) {
                $table->dropColumn('fine_paid_at');
            }
        });
    }
};
