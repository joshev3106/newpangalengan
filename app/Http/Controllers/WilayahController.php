<?php

// app/Http/Controllers/WilayahController.php
namespace App\Http\Controllers;

use App\Models\Stunting;
use App\Models\DesaProfile;
use App\Models\Puskesmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function index(Request $request)
    {
        $latest = Stunting::select('desa', DB::raw('MAX(period) as period'))->groupBy('desa');

        $rows = Stunting::joinSub($latest, 'latest', function ($join) {
                $join->on('stuntings.desa', '=', 'latest.desa')
                     ->on('stuntings.period', '=', 'latest.period');
            })
            ->leftJoin('desa_profiles as dp', 'stuntings.desa', '=', 'dp.desa')
            ->leftJoin('puskesmas as pk', 'dp.puskesmas_id', '=', 'pk.id')
            ->select([
                'stuntings.desa',
                'stuntings.populasi',
                'stuntings.kasus',
                'stuntings.period',
                'dp.cakupan',
                'dp.puskesmas_id',
                DB::raw('COALESCE(pk.nama, dp.faskes_terdekat, "") as faskes_nama'),
            ])
            ->orderBy('stuntings.desa')
            ->get();

        // untuk dropdown/select di modal
        $puskesmas = Puskesmas::orderBy('nama')->get(['id','nama']);

        return view('wilayah.index', compact('rows','puskesmas'));
    }

    public function upsert(Request $request)
    {
        $data = $request->validate([
            'desa'         => ['required','string','max:100'],
            'puskesmas_id' => ['nullable','exists:puskesmas,id'],
            'faskes'       => ['nullable','string','max:150'],
            'cakupan'      => ['nullable','integer','between:0,100'],
        ]);

        // Desa harus ada di data stunting
        abort_unless(Stunting::where('desa',$data['desa'])->exists(), 404, 'Desa tidak ditemukan.');

        // Tentukan final puskesmas_id & faskes_terdekat
        $puskesmasId = $data['puskesmas_id'] ?? null;
        $faskesText  = trim((string)($data['faskes'] ?? ''));

        if (!$puskesmasId && $faskesText === '') {
            // Auto-suggest: cari puskesmas dengan nama mengandung nama desa
            $match = Puskesmas::where('nama', 'like', '%'.$data['desa'].'%')->first();
            if ($match) {
                $puskesmasId = $match->id;
            } else {
                // fallback standar
                $namaBersih = preg_replace('/^(desa|kelurahan)\s+/i', '', $data['desa']);
                $faskesText = "Puskesmas {$namaBersih}";
            }
        }

        // Simpan
        DesaProfile::updateOrCreate(
            ['desa' => $data['desa']],
            [
                'puskesmas_id'    => $puskesmasId,
                'faskes_terdekat' => $puskesmasId ? null : ($faskesText ?: null), // kalau pakai id, kosongkan teks
                'cakupan'         => $data['cakupan'] ?? null,
            ]
        );

        return back()->with('ok', 'Profil desa disimpan.');
    }
}
