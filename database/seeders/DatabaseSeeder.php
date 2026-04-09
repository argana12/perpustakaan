<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LibraryMasterSeeder::class,
        ]);

        // 🔐 Create roles
        $adminRole   = Role::firstOrCreate(['name' => 'admin']);
        $petugasRole = Role::firstOrCreate(['name' => 'petugas']);
        $memberRole  = Role::firstOrCreate(['name' => 'member']);

        // 👑 Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@perpus.com'],
            [
                'name' => 'Admin Perpustakaan',
                'password' => Hash::make('password'),
                'member_type' => 'teacher', // admin kita anggap guru
            ]
        );

        // attach role
        $admin->assignRole($adminRole);
    }
}