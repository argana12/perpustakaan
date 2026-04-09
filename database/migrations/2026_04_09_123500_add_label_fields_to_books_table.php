<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'category')) {
                $table->string('category', 100)->nullable()->after('cover_image');
            }
            if (!Schema::hasColumn('books', 'rack_code')) {
                $table->string('rack_code', 30)->nullable()->after('category');
            }
            if (!Schema::hasColumn('books', 'label_color')) {
                $table->string('label_color', 30)->nullable()->after('rack_code');
            }
            if (!Schema::hasColumn('books', 'exemplar_no')) {
                $table->unsignedInteger('exemplar_no')->default(1)->after('label_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            foreach (['category', 'rack_code', 'label_color', 'exemplar_no'] as $column) {
                if (Schema::hasColumn('books', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
