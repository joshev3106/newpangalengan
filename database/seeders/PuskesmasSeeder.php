<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Puskesmas;

class PuskesmasSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['nama' => 'Puskesmas Pangalengan', 'tipe'=>'induk',     'lat'=>-7.3067,'lng'=>107.5933],
            ['nama' => 'Puskesmas Margamulya',  'tipe'=>'pembantu',  'lat'=>-7.3167,'lng'=>107.5833],
            ['nama' => 'Puskesmas Warnasari',   'tipe'=>'pembantu',  'lat'=>-7.3267,'lng'=>107.5733],
            ['nama' => 'Posyandu Melati',       'tipe'=>'posyandu',  'lat'=>-7.3367,'lng'=>107.5633],
            ['nama' => 'Posyandu Mawar',        'tipe'=>'posyandu',  'lat'=>-7.2967,'lng'=>107.6033],
        ];
        foreach ($rows as $r) {
            Puskesmas::updateOrCreate(['nama'=>$r['nama']], $r);
        }
    }
}

