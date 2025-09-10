<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataDesa extends Seeder
{
    public function run(): void
    {
        $now = now();

        $data = [
            ['desa' => 'Banjarsari',     'kasus' => 588,  'populasi' => 4899, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~12%
            ['desa' => 'Lamajang',       'kasus' => 916,  'populasi' => 2617, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~35%
            ['desa' => 'Margaluyu',      'kasus' => 612,  'populasi' => 2782, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~22%
            ['desa' => 'Margamekar',     'kasus' => 189,  'populasi' => 2362, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~8%
            ['desa' => 'Margamukti',     'kasus' => 1058, 'populasi' => 3920, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~27%
            ['desa' => 'Margamulya',     'kasus' => 575,  'populasi' => 3836, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~15%
            ['desa' => 'Pangalengan',    'kasus' => 1998, 'populasi' => 4163, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~48%
            ['desa' => 'Pulosari',       'kasus' => 233,  'populasi' => 4656, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~5%
            ['desa' => 'Sukalayu',       'kasus' => 134,  'populasi' =>  747, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~18%
            ['desa' => 'Sukamanah',      'kasus' => 340,  'populasi' =>  548, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~62%
            ['desa' => 'Tribaktimulya',  'kasus' => 251,  'populasi' => 1045, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~24%
            ['desa' => 'Wanasuka',       'kasus' => 1345, 'populasi' => 4338, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~31%
            ['desa' => 'Warnasari',      'kasus' => 564,  'populasi' => 1045, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~54%
        ];


        // Ganti 'stunting' sesuai nama tabel kamu
        // Upsert berdasarkan kombinasi unik (desa, period)
        DB::table('stuntings')->upsert(
            $data,
            ['desa', 'period'],              // kolom kunci unik
            ['kasus', 'populasi', 'updated_at'] // kolom yang di-update jika sudah ada
        );
    }
}
