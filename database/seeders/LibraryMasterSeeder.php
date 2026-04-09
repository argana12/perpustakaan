<?php

namespace Database\Seeders;

use App\Models\BookCategory;
use App\Models\LabelColor;
use App\Models\Rack;
use Illuminate\Database\Seeder;

class LibraryMasterSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'NOVEL',
            'SAINS',
            'SEJARAH',
            'AGAMA',
            'TEKNOLOGI',
            'BIOGRAFI',
            'PSIKOLOGI',
            'EKONOMI',
            'MATEMATIKA',
            'FISIKA',
            'KIMIA',
            'BIOLOGI',
            'GEOGRAFI',
            'BAHASA',
            'KOMIK',
            'SASTRA',
            'PENDIDIKAN',
            'FILSAFAT',
            'HUKUM',
            'KESEHATAN',
            'SENI',
            'MANAJEMEN',
        ];

        $colors = [
            ['name' => 'Biru', 'hex' => '#2563EB'],
            ['name' => 'Merah', 'hex' => '#DC2626'],
            ['name' => 'Hijau', 'hex' => '#16A34A'],
            ['name' => 'Kuning', 'hex' => '#EAB308'],
            ['name' => 'Ungu', 'hex' => '#7C3AED'],
            ['name' => 'Oranye', 'hex' => '#EA580C'],
            ['name' => 'Abu', 'hex' => '#6B7280'],
            ['name' => 'Coklat', 'hex' => '#92400E'],
        ];

        $racks = [
            ['code' => 'R1-A1', 'name' => 'Rak Novel Lokal'],
            ['code' => 'R1-A2', 'name' => 'Rak Novel Terjemahan'],
            ['code' => 'R1-A3', 'name' => 'Rak Sejarah'],
            ['code' => 'R1-B1', 'name' => 'Rak Agama'],
            ['code' => 'R1-B2', 'name' => 'Rak Psikologi'],
            ['code' => 'R2-A1', 'name' => 'Rak Sains Dasar'],
            ['code' => 'R2-A2', 'name' => 'Rak Fisika dan Kimia'],
            ['code' => 'R2-A3', 'name' => 'Rak Biologi'],
            ['code' => 'R2-B1', 'name' => 'Rak Teknologi'],
            ['code' => 'R2-B2', 'name' => 'Rak Matematika'],
            ['code' => 'R3-A1', 'name' => 'Rak Pendidikan'],
            ['code' => 'R3-A2', 'name' => 'Rak Bahasa dan Sastra'],
            ['code' => 'R3-B1', 'name' => 'Rak Ekonomi Manajemen'],
            ['code' => 'R3-B2', 'name' => 'Rak Hukum'],
            ['code' => 'R4-A1', 'name' => 'Rak Kesehatan'],
        ];

        foreach ($categories as $category) {
            BookCategory::firstOrCreate(['name' => $category]);
        }

        foreach ($colors as $color) {
            LabelColor::firstOrCreate(['name' => $color['name']], ['hex' => $color['hex']]);
        }

        foreach ($racks as $rack) {
            Rack::firstOrCreate(['code' => $rack['code']], ['name' => $rack['name']]);
        }
    }
}
