<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Puskesmas;

class PuskesmasSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'nama' => 'RSU Karya Pangalengan Bhakti Sehat (KPBS)', 
                'tipe'=> 'induk',     
                'lat' => -7.176367844945937, 
                'lng' => 107.57284133880104,
            ],
            [
                'nama' => 'Puskesmas Pangalengan', 
                'tipe'=> 'Pembantu',     
                'lat' => -7.176367844945937, 
                'lng' => 107.57107108087415,
            ],
        ];
        foreach ($rows as $r) {
            Puskesmas::updateOrCreate(['nama'=>$r['nama']], $r);
        }
    }
}

