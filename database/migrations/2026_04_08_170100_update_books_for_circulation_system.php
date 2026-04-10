<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'code')) {
                $table->string('code', 20)->nullable()->unique()->after('id');
            }

            if (!Schema::hasColumn('books', 'pages')) {
                $table->unsignedInteger('pages')->default(1)->after('isbn');
            }

            if (!Schema::hasColumn('books', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('pages');
            }
        });

        Schema::table('books', function (Blueprint $table) {
            $table->enum('status', ['available', 'borrowed', 'reserved', 'lost'])->default('available')->change();
        });

        $books = DB::table('books')->whereNull('code')->orderBy('id')->get(['id']);
        foreach ($books as $book) {
            DB::table('books')
                ->where('id', $book->id)
                ->update(['code' => 'BK' . str_pad((string) $book->id, 3, '0', STR_PAD_LEFT)]);
        }
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->enum('status', ['available', 'borrowed'])->default('available')->change();
        });

        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'cover_image')) {
                $table->dropColumn('cover_image');
            }

            if (Schema::hasColumn('books', 'pages')) {
                $table->dropColumn('pages');
            }

            if (Schema::hasColumn('books', 'code')) {
                $table->dropUnique(['code']);
                $table->dropColumn('code');
            }
        });
    }
};
