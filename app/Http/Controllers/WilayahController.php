<?php

// app/Http/Controllers/WilayahController.php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Stunting;
use App\Models\Puskesmas;
use App\Models\DesaProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function index(Request $request)
    {
    
        $desaInput = trim((string) $request->query('desa', ''));
        $startM = trim((string) $request->query('start', ''));
        $endM   = trim((string) $request->query('end', ''));

        // Normalisasi YYYY-MM → YYYY-MM-01 / endOfMonth
        $startDate = null; $endDate = null;
        try { if ($startM !== '') $startDate = Carbon::createFromFormat('Y-m', $startM)->startOfMonth()->toDateString(); } catch (\Throwable $e) {}
        try { if ($endM   !== '') $endDate   = Carbon::createFromFormat('Y-m', $endM)->endOfMonth()->toDateString();   } catch (\Throwable $e) {}

        // Jika user isi rentang tapi belum isi desa → tolak
        if (($startDate || $endDate) && $desaInput === '') {
            return back()->withErrors(['desa' => 'Silakan isi nama desa terlebih dahulu untuk menelusuri rentang periode.'])
                         ->withInput();
        }

        // Label range (untuk banner)
        $rangeLabel = null;
        if ($startDate || $endDate) {
            $sLabel = $startDate ? Carbon::parse($startDate)->isoFormat("MMM 'YY") : '…';
            $eLabel = $endDate   ? Carbon::parse($endDate)->isoFormat("MMM 'YY")   : '…';
            $rangeLabel = $sLabel.' – '.$eLabel;
        }

        // MODE A: Desa + Range → tampilkan daftar per-periode untuk desa itu
        if ($desaInput !== '' && ($startDate || $endDate)) {
            $rows = Stunting::query()
                ->where('stuntings.desa', 'like', '%'.$desaInput.'%')
                ->when($startDate, fn($q) => $q->whereDate('stuntings.period', '>=', $startDate))
                ->when($endDate,   fn($q) => $q->whereDate('stuntings.period', '<=', $endDate))
                ->leftJoin('desa_profiles as dp', 'stuntings.desa', '=', 'dp.desa')
                ->leftJoin('puskesmas as pk', 'dp.puskesmas_id', '=', 'pk.id')
                ->orderBy('stuntings.desa')->orderBy('stuntings.period')
                ->get([
                    'stuntings.desa',
                    'stuntings.populasi',
                    'stuntings.kasus',
                    'stuntings.period',
                    'dp.cakupan',
                    'dp.puskesmas_id',
                    DB::raw('COALESCE(pk.nama, dp.faskes_terdekat, "") as faskes_nama'),
                ]);

            // Update terakhir & avg rate di rentang
            $maxPeriodRaw = Stunting::query()
                ->where('stuntings.desa', 'like', '%'.$desaInput.'%')
                ->when($startDate, fn($q) => $q->whereDate('period', '>=', $startDate))
                ->when($endDate,   fn($q) => $q->whereDate('period', '<=', $endDate))
                ->max('period');

            $lastUpdateLabel = $maxPeriodRaw ? Carbon::parse($maxPeriodRaw)->isoFormat("MMM 'YY") : null;

            $trendRows = Stunting::query()
                ->where('stuntings.desa', 'like', '%'.$desaInput.'%')
                ->when($startDate, fn($q) => $q->whereDate('period', '>=', $startDate))
                ->when($endDate,   fn($q) => $q->whereDate('period', '<=', $endDate))
                ->selectRaw("DATE_FORMAT(period,'%Y-%m') as ym")
                ->selectRaw("SUM(kasus) as kasus, SUM(populasi) as pop")
                ->groupBy('ym')->orderBy('ym')
                ->get();

            $avgRatePage = $trendRows->count()
                ? round($trendRows->map(fn($r) => $r->pop > 0 ? ($r->kasus/$r->pop*100) : 0)->avg(), 1)
                : 0.0;

            $puskesmas = Puskesmas::orderBy('nama')->get(['id','nama']);

            // displayPeriodLabel (untuk “data terbaru”) tidak relevan di mode range
            $displayPeriodLabel = null;
            $desa = $desaInput;

            return view('wilayah.index', compact(
                'rows','puskesmas','rangeLabel','lastUpdateLabel','avgRatePage','desa','displayPeriodLabel'
            ));
        }

        // MODE B: Tanpa range → data terbaru per desa (default)
        $latest = Stunting::select('desa', DB::raw('MAX(period) as last_period'))
            ->groupBy('desa');

        $base = Stunting::joinSub($latest,'latest',function($j){
                    $j->on('stuntings.desa','=','latest.desa')
                      ->on('stuntings.period','=','latest.last_period');
                })
                ->leftJoin('desa_profiles as dp', 'stuntings.desa', '=', 'dp.desa')
                ->leftJoin('puskesmas as pk', 'dp.puskesmas_id', '=', 'pk.id');

        // optional: kalau desa diisi TANPA range → filter ke desa tsb tapi tetap ambil terbaru
        if ($desaInput !== '') {
            $base->where('stuntings.desa','like','%'.$desaInput.'%');
        }

        $rows = $base->orderBy('stuntings.desa')
            ->get([
                'stuntings.desa',
                'stuntings.populasi',
                'stuntings.kasus',
                'stuntings.period',
                'dp.cakupan',
                'dp.puskesmas_id',
                DB::raw('COALESCE(pk.nama, dp.faskes_terdekat, "") as faskes_nama'),
            ]);

        // Kartu ringkas (global terbaru)
        $maxPeriodRaw = Stunting::max('period');
        $lastUpdateLabel = $maxPeriodRaw ? Carbon::parse($maxPeriodRaw)->isoFormat("MMM 'YY") : null;
        $displayPeriodLabel = $lastUpdateLabel;

        // avg rate global terbaru per bulan (opsional tetap, boleh seperti sebelumnya)
        $trendRows = Stunting::selectRaw("DATE_FORMAT(period,'%Y-%m') as ym")
            ->selectRaw("SUM(kasus) as kasus, SUM(populasi) as pop")
            ->groupBy('ym')->orderBy('ym')->get();
        $avgRatePage = $trendRows->count()
            ? round($trendRows->map(fn($r) => $r->pop>0 ? ($r->kasus/$r->pop*100) : 0)->avg(), 1)
            : 0.0;

        $puskesmas = Puskesmas::orderBy('nama')->get(['id','nama']);
        $desa = $desaInput ?: null;

        return view('wilayah.index', compact(
            'rows','puskesmas','rangeLabel','displayPeriodLabel','lastUpdateLabel','avgRatePage','desa'
        ));
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
