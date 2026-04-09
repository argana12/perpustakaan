<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('book_categories', 'label_color')) {
                $table->string('label_color', 30)->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('book_categories', function (Blueprint $table) {
            if (Schema::hasColumn('book_categories', 'label_color')) {
                $table->dropColumn('label_color');
            }
        });
    }
};
