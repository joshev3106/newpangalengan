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
            ['desa' => 'Banjarsari',     'kasus' => 520,  'populasi' => 5055, 'period' => '2025-08-01', 'created_at' => $now, 'updated_at' => $now], // ~10%
            ['desa' => 'Lamajang',       'kasus' => 905,  'populasi' => 2830, 'period' => '2025-08-01', 'created_at' => $now, 'updated_at' => $now], // ~32%
            ['desa' => 'Margaluyu',      'kasus' => 440,  'populasi' => 2980, 'period' => '2025-08-01', 'created_at' => $now, 'updated_at' => $now], // ~15%
            ['desa' => 'Margamekar',     'kasus' => 310,  'populasi' => 2475, 'period' => '2025-08-01', 'created_at' => $now, 'updated_at' => $now], // ~12%
            ['desa' => 'Margamukti',     'kasus' => 1250, 'populasi' => 4075, 'period' => '2025-08-01', 'created_at' => $now, 'updated_at' => $now], // ~31%
            ['desa' => 'Margamulya',     'kasus' => 600,  'populasi' => 3725, 'period' => '2025-08-01', 'created_at' => $now, 'updated_at' => $now], // ~16%
            ['desa' => 'Pangalengan',    'kasus' => 1775, 'populasi' => 4420, 'period' => '2025-08-01', 'created_at' => $now, 'updated_at' => $now], // ~40%
            ['desa' => 'Pulosari',       'kasus' => 265,  'populasi' => 4755, 'period' => '2025-08-01', 'created_at' => $now, 'updated_at' => $now], // ~5%
            ['desa' => 'Sukaluyu',       'kasus' => 195,  'populasi' =>  830, 'period' => '2025-08-01', 'created_at' => $now, 'updated_at' => $now], // ~23%
            ['desa' => 'Sukamanah',      'kasus' => 338,  'populasi' =>  690, 'period' => '2025-08-01', 'created_at' => $now, 'updated_at' => $now], // ~49%
            ['desa' => 'Tribaktimulya',  'kasus' => 310,  'populasi' => 1205, 'period' => '2025-08-01', 'created_at' => $now, 'updated_at' => $now], // ~26%
            ['desa' => 'Wanasuka',       'kasus' => 995,  'populasi' => 4310, 'period' => '2025-08-01', 'created_at' => $now, 'updated_at' => $now], // ~23%
            ['desa' => 'Warnasari',      'kasus' => 685,  'populasi' => 1275, 'period' => '2025-08-01', 'created_at' => $now, 'updated_at' => $now], // ~54%

            ['desa' => 'Banjarsari',     'kasus' => 588,  'populasi' => 4899, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~12%
            ['desa' => 'Lamajang',       'kasus' => 916,  'populasi' => 2617, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~35%
            ['desa' => 'Margaluyu',      'kasus' => 612,  'populasi' => 2782, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~22%
            ['desa' => 'Margamekar',     'kasus' => 189,  'populasi' => 2362, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~8%
            ['desa' => 'Margamukti',     'kasus' => 1058, 'populasi' => 3920, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~27%
            ['desa' => 'Margamulya',     'kasus' => 575,  'populasi' => 3836, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~15%
            ['desa' => 'Pangalengan',    'kasus' => 1998, 'populasi' => 4163, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~48%
            ['desa' => 'Pulosari',       'kasus' => 233,  'populasi' => 4656, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~5%
            ['desa' => 'Sukaluyu',       'kasus' => 134,  'populasi' =>  747, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~18%
            ['desa' => 'Sukamanah',      'kasus' => 340,  'populasi' =>  548, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~62%
            ['desa' => 'Tribaktimulya',  'kasus' => 251,  'populasi' => 1045, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~24%
            ['desa' => 'Wanasuka',       'kasus' => 1345, 'populasi' => 4338, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~31%
            ['desa' => 'Warnasari',      'kasus' => 564,  'populasi' => 1045, 'period' => '2025-09-01', 'created_at' => $now, 'updated_at' => $now], // ~54%

            ['desa' => 'Banjarsari',     'kasus' => 420,  'populasi' => 5120, 'period' => '2025-10-01', 'created_at' => $now, 'updated_at' => $now], // ~8%
            ['desa' => 'Lamajang',       'kasus' => 780,  'populasi' => 2890, 'period' => '2025-10-01', 'created_at' => $now, 'updated_at' => $now], // ~27%
            ['desa' => 'Margaluyu',      'kasus' => 355,  'populasi' => 3120, 'period' => '2025-10-01', 'created_at' => $now, 'updated_at' => $now], // ~11%
            ['desa' => 'Margamekar',     'kasus' => 265,  'populasi' => 2495, 'period' => '2025-10-01', 'created_at' => $now, 'updated_at' => $now], // ~10%
            ['desa' => 'Margamukti',     'kasus' => 1220, 'populasi' => 4050, 'period' => '2025-10-01', 'created_at' => $now, 'updated_at' => $now], // ~30%
            ['desa' => 'Margamulya',     'kasus' => 688,  'populasi' => 3710, 'period' => '2025-10-01', 'created_at' => $now, 'updated_at' => $now], // ~18%
            ['desa' => 'Pangalengan',    'kasus' => 1540, 'populasi' => 4325, 'period' => '2025-10-01', 'created_at' => $now, 'updated_at' => $now], // ~35%
            ['desa' => 'Pulosari',       'kasus' => 310,  'populasi' => 4780, 'period' => '2025-10-01', 'created_at' => $now, 'updated_at' => $now], // ~6%
            ['desa' => 'Sukaluyu',       'kasus' => 189,  'populasi' =>  820, 'period' => '2025-10-01', 'created_at' => $now, 'updated_at' => $now], // ~23%
            ['desa' => 'Sukamanah',      'kasus' => 278,  'populasi' =>  610, 'period' => '2025-10-01', 'created_at' => $now, 'updated_at' => $now], // ~46%
            ['desa' => 'Tribaktimulya',  'kasus' => 340,  'populasi' => 1120, 'period' => '2025-10-01', 'created_at' => $now, 'updated_at' => $now], // ~30%
            ['desa' => 'Wanasuka',       'kasus' => 980,  'populasi' => 4210, 'period' => '2025-10-01', 'created_at' => $now, 'updated_at' => $now], // ~23%
            ['desa' => 'Warnasari',      'kasus' => 720,  'populasi' => 1290, 'period' => '2025-10-01', 'created_at' => $now, 'updated_at' => $now], // ~55%

            ['desa' => 'Banjarsari',     'kasus' => 505,  'populasi' => 4980, 'period' => '2025-11-01', 'created_at' => $now, 'updated_at' => $now], // ~10%
            ['desa' => 'Lamajang',       'kasus' => 1012, 'populasi' => 3125, 'period' => '2025-11-01', 'created_at' => $now, 'updated_at' => $now], // ~32%
            ['desa' => 'Margaluyu',      'kasus' => 490,  'populasi' => 2880, 'period' => '2025-11-01', 'created_at' => $now, 'updated_at' => $now], // ~17%
            ['desa' => 'Margamekar',     'kasus' => 345,  'populasi' => 2545, 'period' => '2025-11-01', 'created_at' => $now, 'updated_at' => $now], // ~13%
            ['desa' => 'Margamukti',     'kasus' => 980,  'populasi' => 3925, 'period' => '2025-11-01', 'created_at' => $now, 'updated_at' => $now], // ~25%
            ['desa' => 'Margamulya',     'kasus' => 710,  'populasi' => 3860, 'period' => '2025-11-01', 'created_at' => $now, 'updated_at' => $now], // ~18%
            ['desa' => 'Pangalengan',    'kasus' => 1720, 'populasi' => 4450, 'period' => '2025-11-01', 'created_at' => $now, 'updated_at' => $now], // ~38%
            ['desa' => 'Pulosari',       'kasus' => 280,  'populasi' => 4710, 'period' => '2025-11-01', 'created_at' => $now, 'updated_at' => $now], // ~6%
            ['desa' => 'Sukaluyu',       'kasus' => 220,  'populasi' =>  905, 'period' => '2025-11-01', 'created_at' => $now, 'updated_at' => $now], // ~24%
            ['desa' => 'Sukamanah',      'kasus' => 392,  'populasi' =>  655, 'period' => '2025-11-01', 'created_at' => $now, 'updated_at' => $now], // ~59%
            ['desa' => 'Tribaktimulya',  'kasus' => 290,  'populasi' => 1175, 'period' => '2025-11-01', 'created_at' => $now, 'updated_at' => $now], // ~25%
            ['desa' => 'Wanasuka',       'kasus' => 1225, 'populasi' => 4395, 'period' => '2025-11-01', 'created_at' => $now, 'updated_at' => $now], // ~28%
            ['desa' => 'Warnasari',      'kasus' => 610,  'populasi' => 1190, 'period' => '2025-11-01', 'created_at' => $now, 'updated_at' => $now], // ~51%

            ['desa' => 'Banjarsari',     'kasus' => 465,  'populasi' => 4820, 'period' => '2025-12-01', 'created_at' => $now, 'updated_at' => $now], // ~9%
            ['desa' => 'Lamajang',       'kasus' => 840,  'populasi' => 2975, 'period' => '2025-12-01', 'created_at' => $now, 'updated_at' => $now], // ~28%
            ['desa' => 'Margaluyu',      'kasus' => 520,  'populasi' => 3020, 'period' => '2025-12-01', 'created_at' => $now, 'updated_at' => $now], // ~17%
            ['desa' => 'Margamekar',     'kasus' => 295,  'populasi' => 2610, 'period' => '2025-12-01', 'created_at' => $now, 'updated_at' => $now], // ~11%
            ['desa' => 'Margamukti',     'kasus' => 1140, 'populasi' => 4180, 'period' => '2025-12-01', 'created_at' => $now, 'updated_at' => $now], // ~27%
            ['desa' => 'Margamulya',     'kasus' => 640,  'populasi' => 3655, 'period' => '2025-12-01', 'created_at' => $now, 'updated_at' => $now], // ~17%
            ['desa' => 'Pangalengan',    'kasus' => 1635, 'populasi' => 4370, 'period' => '2025-12-01', 'created_at' => $now, 'updated_at' => $now], // ~37%
            ['desa' => 'Pulosari',       'kasus' => 355,  'populasi' => 4865, 'period' => '2025-12-01', 'created_at' => $now, 'updated_at' => $now], // ~7%
            ['desa' => 'Sukaluyu',       'kasus' => 205,  'populasi' =>  875, 'period' => '2025-12-01', 'created_at' => $now, 'updated_at' => $now], // ~23%
            ['desa' => 'Sukamanah',      'kasus' => 312,  'populasi' =>  640, 'period' => '2025-12-01', 'created_at' => $now, 'updated_at' => $now], // ~48%
            ['desa' => 'Tribaktimulya',  'kasus' => 375,  'populasi' => 1240, 'period' => '2025-12-01', 'created_at' => $now, 'updated_at' => $now], // ~30%
            ['desa' => 'Wanasuka',       'kasus' => 1080, 'populasi' => 4285, 'period' => '2025-12-01', 'created_at' => $now, 'updated_at' => $now], // ~25%
            ['desa' => 'Warnasari',      'kasus' => 745,  'populasi' => 1320, 'period' => '2025-12-01', 'created_at' => $now, 'updated_at' => $now], // ~56%
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
