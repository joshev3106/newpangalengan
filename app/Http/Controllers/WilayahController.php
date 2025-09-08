<?php

namespace App\Http\Controllers;

use App\Models\Stunting;
use App\Models\DesaProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    // LIST: ambil stunting terbaru per desa + join profil desa
    public function index(Request $request)
    {
        // subquery: periode terbaru per desa
        $latest = Stunting::select('desa', DB::raw('MAX(period) as period'))
            ->groupBy('desa');

        $rows = Stunting::joinSub($latest, 'latest', function ($join) {
                $join->on('stuntings.desa', '=', 'latest.desa')
                     ->on('stuntings.period', '=', 'latest.period');
            })
            ->leftJoin('desa_profiles as dp', 'stuntings.desa', '=', 'dp.desa')
            ->select([
                'stuntings.desa',
                'stuntings.populasi',
                'stuntings.kasus',
                'stuntings.period',
                DB::raw('COALESCE(dp.faskes_terdekat, "") as faskes'),
                DB::raw('dp.cakupan as cakupan'),
            ])
            ->orderBy('stuntings.desa')
            ->get();

        return view('wilayah.index', compact('rows'));
    }

    // SIMPAN/UBAH: faskes & cakupan
    public function upsert(Request $request)
    {
        $data = $request->validate([
            'desa'   => ['required','string','max:100'],
            'faskes' => ['nullable','string','max:150'],
            'cakupan'=> ['nullable','integer','between:0,100'],
        ]);

        // validasi desa mesti ada di tabel stuntings (opsional tapi bagus)
        if (! Stunting::where('desa', $data['desa'])->exists()) {
            return back()->withErrors(['desa' => 'Desa tidak ditemukan di data stunting.'])->withInput();
        }

        DesaProfile::updateOrCreate(
            ['desa' => $data['desa']],
            ['faskes_terdekat' => $data['faskes'] ?? null, 'cakupan' => $data['cakupan']]
        );

        return back()->with('ok', 'Profil desa disimpan.');
    }
}