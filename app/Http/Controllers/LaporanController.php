<?php

namespace App\Http\Controllers;

use App\Models\Stunting;
use App\Models\DesaProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    /**
     * Halaman utama laporan.
     * Mengisi semua variabel yang dipakai di view laporan/index.blade.php
     */
    public function index(Request $req)
    {
        // ---------- 1) Baca filter ----------
        $periodType  = $req->query('period_type', 'monthly'); // monthly|quarterly|yearly
        $kind        = $req->query('kind', 'comprehensive');  // stunting|hotspot|coverage|comprehensive
        $template    = $req->query('template', 'executive');  // executive|detailed|comparison
        $desaFilter  = trim((string)$req->query('desa', ''));

        // Normalisasi periode
        $periodDate   = null;       // Carbon startOfMonth (anchor)
        $periodLabel  = '-';        // label human
        $periodValue  = null;       // yang disimpan buat view

        if ($periodType === 'monthly') {
            $str = $req->query('period_value_monthly', '');
            $periodValue = $str;
            if ($str !== '') {
                $periodDate  = Carbon::createFromFormat('Y-m', $str)->startOfMonth();
                $periodLabel = $periodDate->isoFormat("MMM 'YY");
            }
        } elseif ($periodType === 'quarterly') {
            $q  = $req->query('period_value_quarter', 'Q1'); // Q1-Q4
            $yr = (int)$req->query('period_value_year_q', date('Y'));
            $periodValue = sprintf('%d-%s', $yr, $q);
            $firstMonth = ['Q1'=>1,'Q2'=>4,'Q3'=>7,'Q4'=>10][$q] ?? 1;
            $periodDate  = Carbon::create($yr, $firstMonth, 1)->startOfMonth(); // anchor di bulan awal triwulan
            $periodLabel = $q.' '.$yr;
        } else { // yearly
            $yr = (int)$req->query('period_value_year', date('Y'));
            $periodValue = (string)$yr;
            $periodDate  = Carbon::create($yr, 1, 1)->startOfMonth();
            $periodLabel = (string)$yr;
        }

        // ---------- 2) Ambil data stunting sesuai periode ----------
        if ($periodDate) {
            // bulanan: 1 periode; triwulan/tahunan: pakai <= end
            if ($periodType === 'monthly') {
                $rows = Stunting::whereDate('period', $periodDate->toDateString());
            } elseif ($periodType === 'quarterly') {
                $end = (clone $periodDate)->addMonths(2)->endOfMonth();
                $rows = Stunting::whereBetween('period', [$periodDate->toDateString(), $end->toDateString()]);
            } else { // yearly
                $end = (clone $periodDate)->endOfYear();
                $rows = Stunting::whereBetween('period', [$periodDate->toDateString(), $end->toDateString()]);
            }
        } else {
            // default: ambil data terakhir per desa
            $latest = Stunting::select('desa', DB::raw('MAX(period) as last_period'))->groupBy('desa');
            $rows = Stunting::joinSub($latest, 'm', function($j){
                        $j->on('stuntings.desa','=','m.desa')
                          ->on('stuntings.period','=','m.last_period');
                    });
        }

        if ($desaFilter !== '') {
            $rows->where('stuntings.desa','like','%'.$desaFilter.'%');
        }

        $rows = $rows->orderBy('stuntings.desa')->get(['stuntings.*']);

        // ---------- 3) Hitung metrik dasar ----------
        $with = $rows->map(function($r){
            $rate = $r->populasi > 0 ? round(($r->kasus / $r->populasi) * 100, 1) : 0.0;
            $sev  = $rate > 20 ? 'high' : ($rate >= 10 ? 'medium' : ($rate > 0 ? 'low' : 'not'));
            return (object)[
                'desa'     => $r->desa,
                'kasus'    => (int)$r->kasus,
                'populasi' => (int)$r->populasi,
                'period'   => $r->period,
                'rate'     => $rate,
                'severity' => $sev,
            ];
        });

        $avgRate = $with->count()
            ? round($with->avg('rate'), 1)
            : null;

        $hotspot = [
            '99' => $with->where('severity','high')->count(),
            '95' => $with->where('severity','medium')->count(),
            '90' => $with->where('severity','low')->count(),
        ];

        $kpi = [
            'total_desa'   => $with->count(),
            'avg_rate'     => $avgRate,
            'hotspot'      => $hotspot,
            'coverage_avg' => $this->computeCoverageAvg($with), // pakai DesaProfile bila ada
            'updated'      => $this->maxPeriodLabel(),          // label periode terbaru
        ];

        // ---------- 4) Ranking & trend (untuk chart) ----------
        $ranking = $with->sortByDesc('rate')->take(15)
                    ->map(fn($o)=>['desa'=>$o->desa,'rate'=>$o->rate])
                    ->values();

        // trend 12 bulan terakhir (tertimbang Σkasus/Σpopulasi ×100)
        $trendRows = Stunting::query()
            ->when($periodDate, function($q) use($periodType, $periodDate) {
                if ($periodType==='monthly') {
                    $q->whereDate('period','<=',$periodDate->toDateString());
                } elseif ($periodType==='quarterly') {
                    $q->whereDate('period','<=',$periodDate->copy()->addMonths(2)->endOfMonth()->toDateString());
                } else {
                    $q->whereDate('period','<=',$periodDate->copy()->endOfYear()->toDateString());
                }
            })
            ->selectRaw("DATE_FORMAT(period, '%Y-%m') as ym, SUM(kasus) as kasus, SUM(populasi) as pop")
            ->groupBy('ym')->orderBy('ym','desc')->limit(12)->get()->reverse()->values();

        $trend = [
            'labels' => $trendRows->pluck('ym'),
            'values' => $trendRows->map(fn($r)=> $r->pop>0 ? round(($r->kasus/$r->pop)*100,1) : 0.0),
        ];

        // ---------- 5) Data peta tematik ----------
        $coords = config('desa_coords', []);
        $mapData = $with->map(function($o) use ($coords) {
            $pt = $coords[$o->desa] ?? null;
            $conf = $o->rate==0 ? 0 : ($o->rate>20 ? 99 : ($o->rate>=10 ? 95 : 90));
            return [
                'desa'       => $o->desa,
                'lat'        => $pt['lat'] ?? null,
                'lng'        => $pt['lng'] ?? null,
                'rate'       => (float)$o->rate,
                'confidence' => $conf,
            ];
        })->values();

        // ---------- 6) Dummy arsip & bookmark (bisa sambungkan ke tabel jika ada) ----------
        $history = session('lap_hist', [
            ['id'=>1,'title'=>'Laporan Bulan Lalu','kind'=>'comprehensive','period'=>'—','template'=>'executive','when'=>now()->subMonth()->isoFormat('DD MMM YYYY')],
        ]);
        $bookmarks = session('lap_bm', [
            ['id'=>1,'name'=>'Bulanan Komprehensif','params'=>['period_type'=>'monthly','kind'=>'comprehensive','template'=>'executive']],
        ]);

        // ---------- 7) Meta perbandingan (placeholder) ----------
        $comparisonMeta = [
            'basis'   => $periodType==='monthly' ? 'MoM' : ($periodType==='yearly' ? 'YoY' : 'QoQ'),
            'periodA' => $periodLabel,
            'periodB' => 'Periode Sebelumnya',
            'delta'   => 0.0,
        ];

        // ---------- 8) Data untuk header ----------
        $pkCount         = count(config('desa_puskesmas.pk_coords', []));
        $desaMappedCount = count(config('desa_puskesmas.desa_to_pk', []));

        // ---------- 9) Kumpulkan ke view ----------
        return view('laporan.index', [
            // generator
            'periodType'   => $periodType,
            'periodValue'  => $periodValue,
            'desaList'     => array_keys($coords),
            'selectedDesa' => $desaFilter,
            'reportKind'   => $kind,
            'template'     => $template,

            // eksekutif
            'kpi'              => $kpi,
            'displayPeriodLabel'=> $periodLabel,
            'comparisonMeta'   => $comparisonMeta,

            // visual
            'ranking' => $ranking,
            'trend'   => $trend,
            'mapData' => $mapData,

            // arsip & bookmark
            'history'   => $history,
            'bookmarks' => $bookmarks,

            // header chip
            'pkCount'         => $pkCount,
            'desaMappedCount' => $desaMappedCount,
        ]);
    }

    /** Rata-rata cakupan layanan sederhana (jika ada DesaProfile->served/cakupan). */
    private function computeCoverageAvg($with)
    {
        // Ambil dp.cakupan (0-100) bila tersedia
        $desaNames = $with->pluck('desa')->all();
        $dp = DesaProfile::whereIn('desa', $desaNames)->get(['desa','cakupan']);
        if ($dp->isEmpty()) return null;

        $vals = $dp->pluck('cakupan')->filter(fn($v)=>$v!==null)->map(fn($v)=>(float)$v);
        return $vals->count() ? round($vals->avg(), 1) : null;
    }

    /** Label periode terakhir global pada tabel stunting. */
    private function maxPeriodLabel(): string
    {
        $max = Stunting::max('period');
        return $max ? Carbon::parse($max)->isoFormat("MMM 'YY") : '-';
    }

    // ============================= Aksi Tambahan (placeholder aman dipakai) =============================

    /** Export sederhana tanpa paket: kirim CSV kompres (zip) + instruksi. */
    public function export(Request $req)
    {
        $format = $req->input('format', 'pdf'); // pdf|xlsx|pptx (placeholder)
        // Buat CSV kecil dari ranking sebagai contoh
        $csv = "desa,rate\n";
        $latest = Stunting::select('desa', DB::raw('MAX(period) as last_period'))->groupBy('desa');
        $rows = Stunting::joinSub($latest,'m',function($j){
                    $j->on('stuntings.desa','=','m.desa')->on('stuntings.period','=','m.last_period');
               })->get();
        foreach ($rows as $r) {
            $rate = $r->populasi>0 ? round($r->kasus/$r->populasi*100,1) : 0;
            $csv .= "{$r->desa},{$rate}\n";
        }

        $name = 'laporan_ranking.csv';
        return response($csv)
            ->header('Content-Type','text/csv')
            ->header('Content-Disposition',"attachment; filename={$name}");
    }

    /** Jadwal (dummy): hanya simpan ke session. */
    public function schedule(Request $req)
    {
        // Simpan saja ke session sebagai demo
        session()->flash('ok', 'Penjadwalan laporan diaktifkan (dummy). Integrasikan ke queue/cron bila diperlukan.');
        return back();
    }

    public function template()
    {
        return response('<div style="padding:20px;font-family:sans-serif">Halaman Template (placeholder). Silakan integrasikan sesuai kebutuhan.</div>');
    }

    public function branding()
    {
        return response('<div style="padding:20px;font-family:sans-serif">Halaman Branding (placeholder). Unggah logo/cover di sini.</div>');
    }

    public function bookmark(Request $req)
    {
        $existing = session('lap_bm', []);
        $existing[] = ['id'=>count($existing)+1, 'name'=>'Bookmark Baru', 'params'=>$req->all()];
        session(['lap_bm'=>$existing]);
        return back()->with('ok','Bookmark disimpan (session).');
    }

    public function show($id)
    {
        return response("<div style='padding:20px;font-family:sans-serif'>Pratinjau Arsip #{$id} (placeholder)</div>");
    }

    public function applyBookmark($id)
    {
        // Demo: ambil dari session dan redirect ke index dengan query di bookmark tsb
        $bookmarks = session('lap_bm', []);
        $b = collect($bookmarks)->firstWhere('id', (int)$id);
        if (!$b) return redirect()->route('laporan.index');
        return redirect()->route('laporan.index', $b['params'] ?? []);
    }
}
