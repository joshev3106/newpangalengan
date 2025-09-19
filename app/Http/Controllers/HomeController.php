<?php

namespace App\Http\Controllers;

use App\Models\Stunting;
use App\Models\DesaProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Support\StuntingStats;

class HomeController extends Controller
{
    public function index(Request $req)
    {
        $periodM    = trim((string) $req->query('period', '')); // 'YYYY-MM' atau ''
        $periodDate = null;

        if ($periodM !== '') {
            try {
                $periodDate = Carbon::createFromFormat('Y-m', $periodM)->startOfMonth();
            } catch (\Throwable $e) {
                $periodDate = null;
            }
        }

        // ===== Ambil rows sesuai periode (per-desa) =====
        if ($periodDate) {
            $rows = Stunting::query()
                ->whereDate('period', $periodDate->toDateString())
                ->select(['desa','kasus','populasi','period'])
                ->orderBy('desa')
                ->get();

            $displayPeriodLabel = $periodDate->isoFormat("MMM 'YY");
        } else {
            $latest = Stunting::select('desa', DB::raw('MAX(period) as period'))->groupBy('desa');

            $rows = Stunting::joinSub($latest, 'latest', function ($join) {
                    $join->on('stuntings.desa', '=', 'latest.desa')
                         ->on('stuntings.period', '=', 'latest.period');
                })
                ->orderBy('stuntings.desa')
                ->get(['stuntings.desa','stuntings.kasus','stuntings.populasi','stuntings.period']);

            $maxPeriod = $rows->max('period');
            $displayPeriodLabel = $maxPeriod ? Carbon::parse($maxPeriod)->isoFormat("MMM 'YY") : '-';
        }

        // ===== Hitung rate + severity (untuk statistik umum & kartu Stunting) =====
        $withRate = $rows->map(function($r){
            $rate = $r->populasi > 0 ? round(($r->kasus / $r->populasi) * 100, 1) : 0.0;
            $sev  = $rate > 20 ? 'high' : ($rate >= 10 ? 'medium' : ($rate > 0 ? 'low' : 'not'));
            return (object)[
                'desa'     => $r->desa,
                'kasus'    => (int) $r->kasus,
                'populasi' => (int) $r->populasi,
                'period'   => $r->period,
                'rate'     => $rate,
                'severity' => $sev,
            ];
        });

        $stats = [
            'high'   => $withRate->where('severity','high')->count(),
            'medium' => $withRate->where('severity','medium')->count(),
            'low'    => $withRate->where('severity','low')->count(),
            'not'    => $withRate->where('severity','not')->count(),
            'total'  => $withRate->count(),
            'avg'    => StuntingStats::simpleAverageRate($withRate), // rata-rata sederhana per-desa (%)
        ];

        // ======== VARIABEL YANG DIBUTUHKAN BLADE ========

        // 1) Rata-rata Stunting (kartu Stunting)
        $avgRateHome = $stats['avg']; // float|null

        // 2) Top 5 Stunting (tabel kiri)
        $topStunting = $withRate
            ->sortByDesc('rate')
            ->take(5)
            ->map(fn($r) => [
                'desa'      => $r->desa,
                'kasus'     => $r->kasus,
                'populasi'  => $r->populasi,
                'rate'      => $r->rate,
            ])
            ->values();

        // 3) Hotspot cards & Top 5 Hotspot (ikuti logika HotspotController)
        $hotspotRows = $withRate->map(function($s){
            $severity   = $s->rate > 20 ? 'high' : ($s->rate >= 10 ? 'medium' : 'low');
            $confidence = $s->rate == 0 ? 0 : ($severity==='high' ? 99 : ($severity==='medium' ? 95 : 90));
            return [
                'desa'       => $s->desa,
                'cases'      => $s->kasus,
                'population' => $s->populasi,
                'rate'       => $s->rate,
                'severity'   => $severity,
                'confidence' => $confidence,
            ];
        });

        $hotspotStats = [
            'high'   => $hotspotRows->where('confidence', 99)->count(),
            'medium' => $hotspotRows->where('confidence', 95)->count(),
            'low'    => $hotspotRows->where('confidence', 90)->count(),
            'not'    => $hotspotRows->where('confidence', 0)->count(),
            'total'  => $hotspotRows->count(),
        ];

        $topHotspots = $hotspotRows
            ->sortByDesc(fn($h) => [$h['confidence'], $h['rate'], $h['cases']])
            ->take(5)
            ->values();

        // 4) Ringkas Wilayah (cakupan rata-rata & desa terdata)
        //    pakai DesaProfile: served_calc = dp.served ? dp.served : (dp.cakupan% * populasi)
        $profiles = DesaProfile::whereIn('desa', $withRate->pluck('desa'))->get()->keyBy('desa');

        $covValues = $withRate->map(function($r) use ($profiles) {
            $dp = $profiles->get($r->desa);
            if (!$dp) return null;

            $servedCalc = null;
            if (!is_null($dp->served)) {
                $servedCalc = (int) round($dp->served);
            } elseif (!is_null($dp->cakupan)) {
                $servedCalc = (int) round(($dp->cakupan / 100) * $r->populasi);
            }

            if ($servedCalc === null || (int)$r->kasus <= 0) return null;
            $pct = ($servedCalc / max(1,(int)$r->kasus)) * 100;
            return max(0, min(100, $pct));
        })->filter(fn($v) => $v !== null);

        $wilayahAvgCov    = $covValues->count() ? round($covValues->avg(), 1) : null;
        $wilayahDesaCount = $withRate->count();

        // 5) Peta / puskesmas
        $puskesmasCount = is_array(config('desa_puskesmas.pk_coords', []))
            ? count(config('desa_puskesmas.pk_coords', [])) : 0;

        // (tetap kirim juga variabel lama yang dipakai di hero/header)
        $pkCount         = $puskesmasCount;
        $desaMappedCount = count(config('desa_puskesmas.desa_to_pk', []));

        return view('home.index', [
            'pageTitle'           => 'Home',
            'period'              => $periodM,
            'displayPeriodLabel'  => $displayPeriodLabel,

            // untuk hero/header lama
            'stats'               => $stats,
            'rows'                => $withRate,
            'pkCount'             => $pkCount,
            'desaMappedCount'     => $desaMappedCount,

            // untuk kartu ringkas & tabel ringkas
            'avgRateHome'         => $avgRateHome,
            'hotspotStats'        => $hotspotStats,
            'topStunting'         => $topStunting,
            'topHotspots'         => $topHotspots,
            'wilayahAvgCov'       => $wilayahAvgCov,
            'wilayahDesaCount'    => $wilayahDesaCount,
            'puskesmasCount'      => $puskesmasCount,
        ]);
    }
}
