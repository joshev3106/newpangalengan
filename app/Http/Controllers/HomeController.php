<?php

namespace App\Http\Controllers;

use App\Models\Stunting;
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

        // ===== Ambil rows sesuai periode =====
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

        // ===== Hitung rate & severity =====
        $withRate = $rows->map(function($r){
            $rate = $r->populasi > 0 ? round(($r->kasus / $r->populasi) * 100, 1) : 0;
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
            'avg'    => StuntingStats::simpleAverageRate($withRate),
        ];

        $top5 = $withRate->sortByDesc('rate')->take(5)->values();

        $pkCount         = count(config('desa_puskesmas.pk_coords', []));
        $desaMappedCount = count(config('desa_puskesmas.desa_to_pk', []));

        // ⬇️ markers peta faskes
        $pkCoords  = config('desa_puskesmas.pk_coords', []);
        $pkMarkers = collect($pkCoords)->map(function ($coord, $name) {
            return [
                'puskesmas' => $name,
                'lat'       => $coord['lat'] ?? null,
                'lng'       => $coord['lng'] ?? null,
                'address'   => $coord['address'] ?? null,
                'tipe'      => $coord['tipe'] ?? null,
            ];
        })->values();

        return view('home.index', [
            'pageTitle'           => 'Home',
            'period'              => $periodM,
            'displayPeriodLabel'  => $displayPeriodLabel,
            'stats'               => $stats,
            'rows'                => $withRate,
            'top5'                => $top5,
            'pkCount'             => $pkCount,
            'desaMappedCount'     => $desaMappedCount,
            'pkMarkers'           => $pkMarkers,
        ]);
    }
}
