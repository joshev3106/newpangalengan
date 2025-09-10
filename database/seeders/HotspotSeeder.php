<?php

namespace Database\Seeders;

use App\Models\Hotspot;
use Illuminate\Database\Seeder;

class HotspotSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name'=>'Cluster 1 - Margamulya',  'lat'=>-7.3167,'lng'=>107.5833,'confidence'=>99,'cases'=>45],
            ['name'=>'Cluster 2 - Warnasari',   'lat'=>-7.3267,'lng'=>107.5733,'confidence'=>95,'cases'=>32],
            ['name'=>'Cluster 3 - Tribaktimulya','lat'=>-7.3367,'lng'=>107.5633,'confidence'=>90,'cases'=>28],
            ['name'=>'Area Normal 1',           'lat'=>-7.3067,'lng'=>107.5933,'confidence'=>0,'cases'=>12],
            ['name'=>'Area Normal 2',           'lat'=>-7.2967,'lng'=>107.6033,'confidence'=>0,'cases'=>8],
            ['name'=>'Area Normal 3',           'lat'=>-7.3467,'lng'=>107.5533,'confidence'=>0,'cases'=>15],
        ];

        foreach ($rows as $r) Hotspot::updateOrCreate(
            ['name'=>$r['name']],
            $r
        );
    }
}
