<?php

$INDUK = 'RSU Karya Pangalengan Bhakti Sehat (KPBS)';
$PEMBANTU_1 = 'Puskesmas Pangalengan';


return [
    // === Mapping: DESA → PUSKESMAS ===
    'Banjarsari'     => $INDUK,
    'Lamajang'       => $PEMBANTU_1,
    'Margaluyu'      => $PEMBANTU_1,
    'Margamekar'     => $PEMBANTU_1,
    'Margamukti'     => $PEMBANTU_1,
    'Margamulya'     => $PEMBANTU_1,
    'Pangalengan'    => $INDUK,
    'Pulosari'       => $INDUK,
    'Sukaluyu'       => $INDUK,
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
        'Pulosari'       => $INDUK,
        'Sukaluyu'       => $INDUK,
        'Sukamanah'      => $INDUK,
        'Tribaktimulya'  => $INDUK,
        'Wanasuka'       => $INDUK,
        'Warnasari'      => $INDUK,
    ],

    // === Koordinat: PUSKESMAS → {lat, lng} ===
    'pk_coords' => [
        'RSU Karya Pangalengan Bhakti Sehat (KPBS)' => [
            'lat' => -7.176367844945937, 
            'lng' => 107.57284133880104,
            'address' => 'Jl. Raya Pangalengan No.340, Pangalengan, Kec. Pangalengan, Kabupaten Bandung, Jawa Barat 40378',
        ],

        'Puskesmas Pangalengan' => [
            'lat' => -7.175820379945676, 
            'lng' => 107.57101437324157,
            'address' => 'No Jl. Raya Pangalengan No.1, Pangalengan, Kec. Pangalengan, Kabupaten Bandung, Jawa Barat 40378',
        ]

        // 'Puskemas Marga Hayu' => [
        //     'lat' => -7.176367844945937, 
        //     'lng' => 107.57107108087415,
        //     'address' => 'No Jl. Raya Pangalengan No.1, Pangalengan, Kec. Pangalengan, Kabupaten Bandung, Jawa Barat 40378',
        // ],
        // 'Puskesmas Kertasari' => ['lat' => ..., 'lng' => ...],
        // dst…
    ],
];
