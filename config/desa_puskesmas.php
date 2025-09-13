<?php

$INDUK = 'RSU Karya Pangalengan Bhakti Sehat';
$PEMBANTU_1 = 'Puskesmas Pangalengan';
$PEMBANTU_2 = 'Puskesmas Banjarsari';
$PEMBANTU_3 = 'Puskesmas Sukaluyu';
$PEMBANTU_4 = 'Puskesmas Lamajang';


return [
    // === Mapping: DESA → PUSKESMAS ===
    'Banjarsari'     => $INDUK,
    'Lamajang'       => $PEMBANTU_1,
    'Margaluyu'      => $PEMBANTU_1,
    'Margamekar'     => $PEMBANTU_1,
    'Margamukti'     => $PEMBANTU_1,
    'Margamulya'     => $PEMBANTU_1,
    'Pangalengan'    => $INDUK,
    'Pulosari'       => $PEMBANTU_2,
    'Sukaluyu'       => $PEMBANTU_3,
    'Sukamanah'      => $INDUK,
    'Tribaktimulya'  => $INDUK,
    'Wanasuka'       => $INDUK,
    'Warnasari'      => $INDUK,
    
    'desa_to_pk' => [
        'Banjarsari'     => $INDUK,
        'Lamajang'       => $PEMBANTU_1,
        'Margaluyu'      => $PEMBANTU_1,
        'Margamekar'     => $PEMBANTU_1,
        'Margamukti'     => $PEMBANTU_1,
        'Margamulya'     => $PEMBANTU_1,
        'Pangalengan'    => $INDUK,
        'Pulosari'       => $PEMBANTU_2,
        'Sukaluyu'       => $PEMBANTU_3,
        'Sukamanah'      => $INDUK,
        'Tribaktimulya'  => $INDUK,
        'Wanasuka'       => $INDUK,
        'Warnasari'      => $INDUK,
    ],

    // === Koordinat: PUSKESMAS → {lat, lng} ===
    'pk_coords' => [
        'RSU Karya Pangalengan Bhakti Sehat' => [
            'lat' => -7.176367844945937, 
            'lng' => 107.57284133880104,
            'tipe' => 'Induk',
            'address' => 'Jl. Raya Pangalengan No.340, Pangalengan, Kec. Pangalengan, Kabupaten Bandung, Jawa Barat 40378',
        ],

        'Puskesmas Pangalengan' => [
            'lat' => -7.175820379945676, 
            'lng' => 107.57101437324157,
            'tipe' => 'Pembantu 1',
            'address' => 'No Jl. Raya Pangalengan No.1, Pangalengan, Kec. Pangalengan, Kabupaten Bandung, Jawa Barat 40378',
        ],

        'Puskesmas Banjarsari' => [
            'lat' => -7.1728540503326935, 
            'lng' => 107.5706737660332,
            'tipe' => 'Pembantu 2',
            'address' => 'No Jl. Raya Pangalengan No.1, Pangalengan, Kec. Pangalengan, Kabupaten Bandung, Jawa Barat 40378',
        ],

        'Puskesmas Sukaluyu' => [
            'lat' => -7.177820379945676, 
            'lng' => 107.57101437324157,
            'tipe' => 'Pembantu 3',
            'address' => 'No Jl. Raya Pangalengan No.1, Pangalengan, Kec. Pangalengan, Kabupaten Bandung, Jawa Barat 40378',
        ],

        'Puskesmas Lamajang' => [
            'lat' => -7.177820379945676, 
            'lng' => 107.57301437324157,
            'tipe' => 'Pembantu 4',
            'address' => 'No Jl. Raya Pangalengan No.1, Pangalengan, Kec. Pangalengan, Kabupaten Bandung, Jawa Barat 40378',
        ],
    ],
];
