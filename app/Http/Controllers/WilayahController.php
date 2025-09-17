<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Stunting;
use App\Models\Puskesmas;
use App\Models\DesaProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\StuntingStats;

class WilayahController extends Controller
{
    public function index(Request $request)
    {
        $desaInput = trim((string) $request->query('desa', ''));
        $startM = trim((string) $request->query('start', ''));
        $endM   = trim((string) $request->query('end', ''));

        // Sorting params
        $sort = $request->query('sort', 'desa');            // 'desa'|'kasus'|'populasi'|'rate'|'faskes_nama'|'cakupan'|'served'
        $dir  = strtolower($request->query('dir', 'asc'));  // 'asc'|'desc'
        $dir  = in_array($dir, ['asc','desc'], true) ? $dir : 'asc';

        // Izinkan kolom 'kasus' untuk sort (tambahan)
        $allowed = ['desa','kasus','populasi','rate','faskes_nama','cakupan','served'];
        if (!in_array($sort, $allowed, true)) $sort = 'desa';

        // rate (%) utk sort
        $rateExpr = "(CASE WHEN stuntings.populasi = 0 THEN 0 ELSE (stuntings.kasus * 100.0) / stuntings.populasi END)";

        /**
         * served:
         * - jika dp.served TIDAK null -> pakai itu
         * - jika null dan dp.cakupan ada -> cakupan% * populasi
         * - jika dua2nya null -> null
         */
        $servedExpr = "(CASE
            WHEN dp.served IS NOT NULL THEN dp.served
            WHEN dp.cakupan IS NULL THEN NULL
            ELSE (dp.cakupan/100.0) * stuntings.populasi
        END)";

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

        // ===================== MODE A: Desa + Range =====================
        if ($desaInput !== '' && ($startDate || $endDate)) {
            $base = Stunting::query()
                ->where('stuntings.desa', 'like', '%'.$desaInput.'%')
                ->when($startDate, fn($q) => $q->whereDate('stuntings.period', '>=', $startDate))
                ->when($endDate,   fn($q) => $q->whereDate('stuntings.period', '<=', $endDate))
                ->leftJoin('desa_profiles as dp', 'stuntings.desa', '=', 'dp.desa')
                ->leftJoin('puskesmas as pk', 'dp.puskesmas_id', '=', 'pk.id')
                ->select([
                    'stuntings.desa',
                    'stuntings.populasi',
                    'stuntings.kasus',
                    'stuntings.period',
                    'dp.cakupan',
                    'dp.served',
                    'dp.puskesmas_id',
                    DB::raw('COALESCE(pk.nama, dp.faskes_terdekat, "") as faskes_nama'),
                    DB::raw("$servedExpr as served_calc"),
                ]);

            // Sorting (MODE A)
            switch ($sort) {
                case 'kasus':
                    $base->orderBy('stuntings.kasus', $dir)->orderBy('stuntings.desa');
                    break;
                case 'populasi':
                    $base->orderBy('stuntings.populasi', $dir)->orderBy('stuntings.desa');
                    break;
                case 'rate':
                    $base->orderByRaw("$rateExpr $dir")->orderBy('stuntings.desa');
                    break;
                case 'faskes_nama':
                    $base->orderBy('faskes_nama', $dir)->orderBy('stuntings.desa');
                    break;
                case 'cakupan':
                    $base->orderByRaw("(dp.cakupan IS NULL) ASC, dp.cakupan $dir")->orderBy('stuntings.desa');
                    break;
                case 'served':
                    $base->orderByRaw("(($servedExpr) IS NULL) ASC, ($servedExpr) $dir")->orderBy('stuntings.desa');
                    break;
                case 'desa':
                default:
                    $base->orderBy('stuntings.desa', $dir)->orderBy('stuntings.period', 'asc');
                    break;
            }

            $rows = $base->get();

            // ===== Rata-rata stunting (rata-rata sederhana per-desa, sama seperti Home)
            $avgRatePage = StuntingStats::simpleAverageRate($rows);

            // Update terakhir (berdasarkan baris yang ditampilkan)
            $maxPeriodRaw = $rows->max('period');
            $lastUpdateLabel = $maxPeriodRaw ? Carbon::parse($maxPeriodRaw)->isoFormat("MMM 'YY") : null;

            $puskesmas = Puskesmas::orderBy('nama')->get(['id','nama']);
            $displayPeriodLabel = null;
            $desa = $desaInput;

            return view('wilayah.index', compact(
                'rows','puskesmas','rangeLabel','lastUpdateLabel','avgRatePage','desa','displayPeriodLabel',
                'sort','dir'
            ));
        }

        // ===================== MODE B: Default (data terbaru per desa) =====================
        $latest = Stunting::select('desa', DB::raw('MAX(period) as last_period'))->groupBy('desa');

        $base = Stunting::joinSub($latest,'latest',function($j){
                    $j->on('stuntings.desa','=','latest.desa')
                      ->on('stuntings.period','=','latest.last_period');
                })
                ->leftJoin('desa_profiles as dp', 'stuntings.desa', '=', 'dp.desa')
                ->leftJoin('puskesmas as pk', 'dp.puskesmas_id', '=', 'pk.id')
                ->select([
                    'stuntings.desa',
                    'stuntings.populasi',
                    'stuntings.kasus',
                    'stuntings.period',
                    'dp.cakupan',
                    'dp.served',
                    'dp.puskesmas_id',
                    DB::raw('COALESCE(pk.nama, dp.faskes_terdekat, "") as faskes_nama'),
                    DB::raw("$servedExpr as served_calc"),
                ]);

        if ($desaInput !== '') {
            $base->where('stuntings.desa','like','%'.$desaInput.'%');
        }

        // Sorting (MODE B)
        switch ($sort) {
            case 'kasus':
                $base->orderBy('stuntings.kasus', $dir)->orderBy('stuntings.desa');
                break;
            case 'populasi':
                $base->orderBy('stuntings.populasi', $dir)->orderBy('stuntings.desa');
                break;
            case 'rate':
                $base->orderByRaw("$rateExpr $dir")->orderBy('stuntings.desa');
                break;
            case 'faskes_nama':
                $base->orderBy('faskes_nama', $dir)->orderBy('stuntings.desa');
                break;
            case 'cakupan':
                $base->orderByRaw("(dp.cakupan IS NULL) ASC, dp.cakupan $dir")->orderBy('stuntings.desa');
                break;
            case 'served':
                $base->orderByRaw("(($servedExpr) IS NULL) ASC, ($servedExpr) $dir")->orderBy('stuntings.desa');
                break;
            case 'desa':
            default:
                $base->orderBy('stuntings.desa', $dir);
                break;
        }

        $rows = $base->get();

        // Kartu ringkas (global label periode terakhir)
        $maxPeriodRaw = Stunting::max('period');
        $lastUpdateLabel = $maxPeriodRaw ? Carbon::parse($maxPeriodRaw)->isoFormat("MMM 'YY") : null;
        $displayPeriodLabel = $lastUpdateLabel;

        // ===== Rata-rata stunting (rata-rata sederhana per-desa, sama seperti Home)
        $avgRatePage = StuntingStats::simpleAverageRate($rows);

        $puskesmas = Puskesmas::orderBy('nama')->get(['id','nama']);
        $desa = $desaInput ?: null;

        return view('wilayah.index', compact(
            'rows','puskesmas','rangeLabel','displayPeriodLabel','lastUpdateLabel','avgRatePage','desa',
            'sort','dir'
        ));
    }

    // ==================== NEW: FORM EDIT ====================
    public function edit(Request $request, string $desa)
    {
        // pastikan desa ada di data stunting
        $latest = Stunting::where('desa', $desa)->orderByDesc('period')->firstOrFail();

        $profile = DesaProfile::firstOrNew(['desa' => $desa]);

        // prefill served: pakai dp.served, kalau null coba hitung dari cakupan
        $currentServed = $profile->served ?? (
            $profile->cakupan !== null
                ? (int) round(($profile->cakupan / 100) * $latest->kasus)
                : null
        );

        // prefill faskes text (pakai text jika ada, kalau tidak pakai nama pk)
        $pkName = $profile->puskesmas_id
            ? optional(Puskesmas::find($profile->puskesmas_id))->nama
            : null;
        $faskesText = $profile->faskes_terdekat ?: $pkName;

        return view('wilayah.edit', [
            'desa'          => $desa,
            'latest'        => $latest,       // punya ->populasi, ->period
            'profile'       => $profile,      // dp record
            'served'        => $currentServed,
            'faskesText'    => $faskesText,
        ]);
    }

    // ==================== NEW: UPDATE ====================
    public function update(Request $request, string $desa)
    {
        $latest = Stunting::where('desa', $desa)->orderByDesc('period')->firstOrFail();

        $data = $request->validate([
            'faskes' => ['nullable','string','max:150'],
            'served' => ['nullable','integer','min:0'],
        ]);

        $served = array_key_exists('served', $data) && $data['served'] !== null
            ? min((int)$data['served'], (int)$latest->populasi)    // clamp ke populasi
            : null;

        // hitung cakupan (%) dari served; biar tampilan existing tetap jalan
        $cakupan = $served !== null && $latest->populasi > 0
            ? min(100, (int) round($served / $latest->populasi * 100))
            : null;

        $profile = DesaProfile::firstOrNew(['desa' => $desa]);

        // Update nilai
        $profile->served  = $served;         // perlu kolom 'served' di desa_profiles
        $profile->cakupan = $cakupan;

        // Faskes Terdekat (text). Kita kosongkan puskesmas_id agar teks ini yang tampil.
        if (array_key_exists('faskes', $data)) {
            $profile->faskes_terdekat = $data['faskes'] ?: null;
            if (!empty($data['faskes'])) {
                $profile->puskesmas_id = null; // prioritaskan teks kustom
            }
        }

        $profile->save();

        return redirect()->route('wilayah.index')->with('ok', 'Profil desa diperbarui.');
    }

    // ==================== (existing) UPSERT DARI MODAL ====================
    public function upsert(Request $request)
    {
        $data = $request->validate([
            'desa'         => ['required','string','max:100'],
            'puskesmas_id' => ['nullable','exists:puskesmas,id'],
            'faskes'       => ['nullable','string','max:150'],
            'cakupan'      => ['nullable','integer','between:0,100'],
        ]);

        abort_unless(Stunting::where('desa',$data['desa'])->exists(), 404, 'Desa tidak ditemukan.');

        $puskesmasId = $data['puskesmas_id'] ?? null;
        $faskesText  = trim((string)($data['faskes'] ?? ''));

        if (!$puskesmasId && $faskesText === '') {
            $match = Puskesmas::where('nama', 'like', '%'.$data['desa'].'%')->first();
            if ($match) {
                $puskesmasId = $match->id;
            } else {
                $namaBersih = preg_replace('/^(desa|kelurahan)\s+/i', '', $data['desa']);
                $faskesText = "Puskesmas {$namaBersih}";
            }
        }

        DesaProfile::updateOrCreate(
            ['desa' => $data['desa']],
            [
                'puskesmas_id'    => $puskesmasId,
                'faskes_terdekat' => $puskesmasId ? null : ($faskesText ?: null),
                'cakupan'         => $data['cakupan'] ?? null,
            ]
        );

        return back()->with('ok', 'Profil desa disimpan.');
    }
}
