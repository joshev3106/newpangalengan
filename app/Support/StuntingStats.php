<?php

namespace App\Support;

class StuntingStats
{
    /**
     * Rata-rata sederhana per-desa (gaya Home):
     * - rate per baris = round((kasus / populasi) * 100, 1); jika pop=0 -> 0
     * - lalu avg dari seluruh rate -> round(..., 1)
     *
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $rows
     * @return float
     */
    public static function simpleAverageRate($rows): float
    {
        if (!$rows || !method_exists($rows, 'avg')) {
            return 0.0;
        }

        $avg = $rows->avg(function ($r) {
            $pop   = (int) ($r->populasi ?? 0);
            $kasus = (int) ($r->kasus ?? 0);
            $rate  = $pop > 0 ? round(($kasus / $pop) * 100, 1) : 0.0;
            return $rate;
        });

        return round($avg ?? 0.0, 1);
    }
}
