<?php

namespace Database\Seeders;

use DateTime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\DesaProfile;
use App\Support\FaskesResolver;

class DataDesa extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ===== Basis populasi per desa =====
        $villages = [
            'Banjarsari'     => 5000,
            'Lamajang'       => 2800,
            'Margaluyu'      => 3000,
            'Margamekar'     => 2500,
            'Margamukti'     => 4000,
            'Margamulya'     => 3700,
            'Pangalengan'    => 4300,
            'Pulosari'       => 4800,
            'Sukaluyu'       => 800,
            'Sukamanah'      => 600,
            'Tribaktimulya'  => 1150,
            'Wanasuka'       => 4300,
            'Warnasari'      => 1200,
        ];

        // ===== Rentang rate (kasus/populasi) per desa =====
        $rateRanges = [
            'Banjarsari'     => [0.08, 0.14],
            'Lamajang'       => [0.25, 0.40],
            'Margaluyu'      => [0.12, 0.22],
            'Margamekar'     => [0.07, 0.15],
            'Margamukti'     => [0.22, 0.35],
            'Margamulya'     => [0.14, 0.22],
            'Pangalengan'    => [0.32, 0.50],
            'Pulosari'       => [0.04, 0.08],
            'Sukaluyu'       => [0.18, 0.28],
            'Sukamanah'      => [0.45, 0.62],
            'Tribaktimulya'  => [0.22, 0.32],
            'Wanasuka'       => [0.20, 0.32],
            'Warnasari'      => [0.45, 0.60],
        ];

        // ====== PERIODE (24 bulan) ======
        $start  = new DateTime('2025-01-01'); // ubah bila perlu
        $months = 24;

        $data = [];

        // NEW: simpan kasus bulan terakhir per desa (untuk clamp served <= kasus)
        $latestKasusByDesa = [];   // [desa => kasus_terakhir]

        for ($m = 0; $m < $months; $m++) {
            $periodDate = (clone $start)->modify("+$m month")->format('Y-m-01');

            foreach ($villages as $desa => $basePop) {
                $seed = $this->seedInt($desa . '|' . $m);

                // jitter populasi ±200
                $popJitter = (($seed >> 8) % 401) - 200; // -200..+200
                $populasi  = max(500, (int) round($basePop + $popJitter));

                // rate dengan variasi musiman halus
                [$rMin, $rMax] = $rateRanges[$desa];
                $monthOfYear   = $m % 12;
                $phase         = (($seed >> 16) % 628) / 100.0; // 0..6.28
                $season        = 1.0 + 0.05 * sin(2 * M_PI * ($monthOfYear / 12.0) + $phase);
                $u             = $this->fracFromSeed($seed);
                $rate          = $rMin + $u * ($rMax - $rMin);
                $rate         *= $season;
                $rate          = $this->clamp($rate, $rMin, $rMax);

                $kasus = (int) round($rate * $populasi);
                if ($kasus > $populasi) $kasus = $populasi;
                if ($kasus < 0)         $kasus = 0;

                $data[] = [
                    'desa'       => $desa,
                    'kasus'      => $kasus,
                    'populasi'   => $populasi,
                    'period'     => $periodDate,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                // simpan kasus terakhir (loop urut naik → nilai terakhir adalah periode terbaru)
                $latestKasusByDesa[$desa] = $kasus;
            }
        }

        // Upsert ke tabel stuntings
        DB::table('stuntings')->upsert(
            $data,
            ['desa', 'period'],
            ['kasus', 'populasi', 'updated_at']
        );

        // ===== NEW: hitung rata-rata rate 12 bulan terakhir per desa =====
        $cutoff = (clone $start)->modify('+' . max(0, $months - 12) . ' month')->format('Y-m-01');
        $agg = []; // [desa => ['kasus' => x, 'populasi' => y]]
        foreach ($data as $row) {
            if ($row['period'] < $cutoff) continue; // pakai 12 bulan terakhir
            $d = $row['desa'];
            if (!isset($agg[$d])) $agg[$d] = ['kasus' => 0, 'populasi' => 0];
            $agg[$d]['kasus']    += $row['kasus'];
            $agg[$d]['populasi'] += $row['populasi'];
        }

        // Desa unik
        $desas = collect($data)->pluck('desa')->unique()->values();

        foreach ($desas as $desa) {
            $profile = DesaProfile::firstOrNew(['desa' => $desa]);

            // Tentukan faskes (seperti sebelumnya)
            if (empty($profile->puskesmas_id) && empty($profile->faskes_terdekat)) {
                $res = FaskesResolver::resolveForDesa($desa);
                $profile->puskesmas_id    = $res['puskesmas_id'];
                $profile->faskes_terdekat = $res['faskes_text'];
            }

            // ===== Isi cakupan jika belum ada (estimasi dari avg rate 12 bulan) =====
            if (is_null($profile->cakupan)) {
                $sumKas = $agg[$desa]['kasus']    ?? 0;
                $sumPop = $agg[$desa]['populasi'] ?? 0;
                $avgRatePct = $sumPop > 0 ? ($sumKas / $sumPop) * 100.0 : 0.0;

                // Model sederhana: cakupan ≈ 95 − 1.6×avgRate ± noise kecil
                $seed   = $this->seedInt('cov|' . $desa);
                $noise  = (($seed >> 10) % 11) - 5; // -5..+5
                $base   = 95 - 1.6 * $avgRatePct;
                $value  = (int) round($this->clamp($base + $noise, 40, 98)); // clamp 40–98%

                $profile->cakupan = $value; // persen
            }

            // ===== NEW: set 'served' & pastikan TIDAK melebihi 'kasus' =====
            $latestKasus = (int) ($latestKasusByDesa[$desa] ?? 0);

            // Jika belum ada served, hitung dari cakupan × kasus TERAKHIR
            if (is_null($profile->served)) {
                if (!is_null($profile->cakupan) && $latestKasus >= 0) {
                    $servedEst = (int) round(($profile->cakupan / 100) * $latestKasus);
                } else {
                    // fallback 60–90% dari kasus terakhir
                    $sSeed     = $this->seedInt('served|' . $desa);
                    $pct       = 0.60 + (($sSeed % 31) / 100); // 0.60..0.90
                    $servedEst = (int) round($pct * $latestKasus);
                }
                // Clamp ke [0, kasus_terakhir]
                $profile->served = max(0, min($servedEst, $latestKasus));
            } else {
                // Kalau sudah ada served, tetap jaga agar <= kasus terbaru
                $profile->served = max(0, min((int)$profile->served, $latestKasus));
            }

            $profile->save();
        }
    }

    // ===== Helpers =====

    /** fractional value 0..1 dari seed int (deterministik). */
    private function fracFromSeed(int $seed): float
    {
        $mod = $seed % 1000;
        if ($mod < 0) $mod += 1000;
        return $mod / 1000.0;
    }

    /** clamp ke [lo, hi]. */
    private function clamp(float $v, float $lo, float $hi): float
    {
        return max($lo, min($hi, $v));
    }

    /** konversi string -> seed int stabil (CRC32B). */
    private function seedInt(string $s): int
    {
        return (int) (hexdec(hash('crc32b', $s)) & 0xFFFFFFFF);
    }
}
