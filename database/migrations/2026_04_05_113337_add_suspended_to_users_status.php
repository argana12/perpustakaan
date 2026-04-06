<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: modify enum column to add 'suspended' value
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM(
            'register',
            'pending',
            'pending_admin',
            'approved',
            'active',
            'rejected',
            'suspended'
        ) NOT NULL DEFAULT 'register'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM(
            'register',
            'pending',
            'pending_admin',
            'approved',
            'active',
            'rejected'
        ) NOT NULL DEFAULT 'register'");
    }
};
