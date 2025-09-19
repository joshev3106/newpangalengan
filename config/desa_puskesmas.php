<?php

$INDUK      = 'RSU Karya Pangalengan Bhakti Sehat';
$PEMBANTU_1      = 'Puskemas Pangalengan';

return [
    // === Mapping: DESA → PUSKESMAS ===
    'Banjarsari'     => $INDUK, // <- sebelumnya $INDUK, sesuaikan jika ini yang diinginkan
    'Lamajang'       => $INDUK,
    'Margaluyu'      => $INDUK,
    'Margamekar'     => $INDUK,
    'Margamukti'     => $INDUK,
    'Margamulya'     => $INDUK,
    'Pangalengan'    => $INDUK,
    'Pulosari'       => $INDUK,
    'Sukaluyu'       => $INDUK,
    'Sukamanah'      => $INDUK, // arahkan ke PK baru
    'Tribaktimulya'  => $INDUK,
    'Wanasuka'       => $INDUK,
    'Warnasari'      => $INDUK,

    'desa_to_pk' => [
        'Banjarsari'     => $INDUK,
        'Lamajang'       => $INDUK,
        'Margaluyu'      => $INDUK,
        'Margamekar'     => $INDUK,
        'Margamukti'     => $INDUK,
        'Margamulya'     => $INDUK,
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
        'RSU Karya Pangalengan Bhakti Sehat' => [
            'lat' => -7.176367844945937,
            'lng' => 107.57284133880104,
            'tipe' => 'Induk',
            'address' => 'Jl. Raya Pangalengan No.340, Pangalengan, Kec. Pangalengan, Kabupaten Bandung, Jawa Barat 40378',
        ],

        'Puskemas Pangalengan' => [
            'lat' => -7.173367844945937,
            'lng' => 107.57284133880104,
            'tipe' => 'Pembantu 1',
            'address' => 'Jl. Raya Pangalengan No.340, Pangalengan, Kec. Pangalengan, Kabupaten Bandung, Jawa Barat 40378',
        ],
    ],
];
