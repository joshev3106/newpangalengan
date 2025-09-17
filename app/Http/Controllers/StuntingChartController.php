<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stunting;
use Carbon\Carbon;
use App\Support\StuntingStats;

/**
 * Endpoint mini-trend 12 bulan (dipakai di Home).
 * Menggunakan rata-rata SEDERHANA per-desa per-bulan (konsisten dgn header Home).
 */
class StuntingChartController extends Controller
{
    public function trend(Request $request)
    {
        // Tentukan anchor (bulan terakhir)
        $periodM = trim((string) $request->query('period', ''));
        $anchor  = null;

        if ($periodM !== '') {
            try {
                $anchor = Carbon::createFromFormat('Y-m', $periodM)->startOfMonth();
            } catch (\Throwable $e) {
                $anchor = null;
            }
        }
        if (!$anchor) {
            $maxPeriod = Stunting::max('period');
            $anchor = $maxPeriod ? Carbon::parse($maxPeriod)->startOfMonth() : now()->startOfMonth();
        }

        // 12 bulan (naik)
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[] = $anchor->copy()->subMonths($i)->startOfMonth();
        }

        $periodStrings = [];
        $trend = [];

        foreach ($months as $m) {
            $periodStrings[] = $m->format('Y-m');

            $rows = Stunting::query()
                ->whereDate('period', $m->toDateString())
                ->select(['desa','kasus','populasi','period'])
                ->get();

            // rata-rata sederhana per-desa (helper yg sama dgn header Home)
            $avg = StuntingStats::simpleAverageRate($rows);
            $trend[] = $avg;
        }

        return response()->json([
            'periods' => $periodStrings,
            'trend'   => $trend,
        ]);
    }
}
