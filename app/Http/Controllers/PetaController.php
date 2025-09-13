<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class PetaController extends Controller
{
    public function index(Request $request)
    {
        // Opsi: filter periode untuk ditampilkan di banner/popup (tidak mempengaruhi warna)
        $periodIn  = trim((string) $request->query('period', '')); // 'YYYY-MM' atau ''
        $periodLbl = $periodIn
            ? Carbon::createFromFormat('Y-m', $periodIn)->isoFormat("MMM 'YY")
            : 'Data terbaru';

        // Ambil koordinat puskesmas dari config
        $cfg      = config('desa_puskesmas', []);
        $pkCoords = $cfg['pk_coords'] ?? [];

        // Bentuk markers: hanya nama + lat/lng
        $markers = collect($pkCoords)->map(function ($coord, $pkName) {
            return [
                'puskesmas' => $pkName,
                'lat'       => $coord['lat'] ?? null,
                'lng'       => $coord['lng'] ?? null,
                'address'   => $coord['address'] ?? null,
                'tipe'      => $coord['tipe'] ?? null,
            ];
        })->values();

        return view('peta.index', [
            'markers'  => $markers,
            'period'   => $periodIn,
            'banner'   => $periodLbl,
            'pkCoords' => config('desa_puskesmas.pk_coords'),
        ]);
    }
}
