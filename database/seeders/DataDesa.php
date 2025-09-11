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

        /**
         * Basis populasi per desa agar angka stabil namun tetap realistis.
         * Nanti tiap bulan diberi variasi kecil (jitter).
         */
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

        /**
         * Rentang rate (kasus/populasi) per desa—mencerminkan karakter desa
         * tapi tetap memberi ruang variasi bulanan.
         */
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

        // ====== SET PERIODE (24 bulan) ======
        $start  = new DateTime('2025-01-01'); // ubah di sini kalau perlu
        $months = 24;                         // 24 bulan = 2 tahun

        $data = [];

        for ($m = 0; $m < $months; $m++) {
            $periodDate = (clone $start)->modify("+$m month")->format('Y-m-01');

            foreach ($villages as $desa => $basePop) {
                // Seed deterministik per (desa, bulan) agar konsisten
                $seed = $this->seedInt($desa . '|' . $m);

                // Variasi populasi bulanan kecil: ±200
                $popJitter = (($seed >> 8) % 401) - 200; // -200..+200
                $populasi  = max(500, (int) round($basePop + $popJitter));

                // Rentang rate per desa + variasi musiman halus (±5%)
                [$rMin, $rMax] = $rateRanges[$desa];
                $monthOfYear   = $m % 12;
                $phase         = (($seed >> 16) % 628) / 100.0; // 0..6.28
                $season        = 1.0 + 0.05 * sin(2 * M_PI * ($monthOfYear / 12.0) + $phase);

                // Variasi acak deterministik dalam rentang + season
                $u    = $this->fracFromSeed($seed); // 0..1
                $rate = $rMin + $u * ($rMax - $rMin);
                $rate *= $season;

                // Clamp agar tetap dalam [rMin, rMax]
                $rate  = $this->clamp($rate, $rMin, $rMax);
                $kasus = (int) round($rate * $populasi);

                // Safety guard
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
            }
        }

        // Upsert ke tabel 'stuntings' berdasarkan kombinasi unik (desa, period)
        DB::table('stuntings')->upsert(
            $data,
            ['desa', 'period'],                  // kunci unik gabungan
            ['kasus', 'populasi', 'updated_at']  // kolom yang di-update jika bentrok
        );

        $desas = collect($data)->pluck('desa')->unique()->values();

        foreach ($desas as $desa) {
            $profile = DesaProfile::firstOrNew(['desa' => $desa]);

            // isi hanya jika keduanya masih kosong (tidak menimpa data manual)
            if (empty($profile->puskesmas_id) && empty($profile->faskes_terdekat)) {
                $res = FaskesResolver::resolveForDesa($desa);
                $profile->puskesmas_id    = $res['puskesmas_id'];
                $profile->faskes_terdekat = $res['faskes_text'];
                $profile->save();
            }
        }
    }

    /**
     * Helper: fractional value 0..1 dari seed int (deterministik).
     */
    private function fracFromSeed(int $seed): float
    {
        $mod = $seed % 1000;
        if ($mod < 0) $mod += 1000;
        return $mod / 1000.0;
    }

    /**
     * Helper: clamp nilai ke [lo, hi].
     */
    private function clamp(float $v, float $lo, float $hi): float
    {
        return max($lo, min($hi, $v));
    }

    /**
     * Helper: jadikan string => seed int stabil memakai CRC32B.
     */
    private function seedInt(string $s): int
    {
        // hexdec(hash('crc32b', ...)) menghasilkan 32-bit unsigned yang stabil.
        // Casting ke int aman dipakai untuk operasi bit (shift/and).
        return (int) (hexdec(hash('crc32b', $s)) & 0xFFFFFFFF);
    }
}
