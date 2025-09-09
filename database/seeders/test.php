<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class test extends Seeder
{
    public function run(): void
    {
        $now = now();

        $data = [
            ['desa' => 'Banjarsari',     'kasus' => 73, 'populasi' => 4899, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now],
            ['desa' => 'Lamajang',       'kasus' => 51, 'populasi' => 2617, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now],
            ['desa' => 'Margaluyu',      'kasus' => 73, 'populasi' => 2782, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now],
            ['desa' => 'Margamekar',     'kasus' => 11, 'populasi' => 2362, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now],
            ['desa' => 'Margamukti',     'kasus' => 13, 'populasi' => 3920, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now],
            ['desa' => 'Margamulya',     'kasus' => 56, 'populasi' => 3836, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now],
            ['desa' => 'Pangalengan',    'kasus' => 68, 'populasi' => 4163, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now],
            ['desa' => 'Pulosari',       'kasus' =>  2, 'populasi' => 4656, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now],
            ['desa' => 'Sukalayu',       'kasus' =>  2, 'populasi' =>  747, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now],
            ['desa' => 'Sukamanah',      'kasus' => 74, 'populasi' =>  548, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now],
            ['desa' => 'Tribaktimulya',  'kasus' => 62, 'populasi' => 1045, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now],
            ['desa' => 'Wanasuka',       'kasus' => 25, 'populasi' => 4338, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now],
            ['desa' => 'Warnasari',      'kasus' => 46, 'populasi' => 1045, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now],
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
