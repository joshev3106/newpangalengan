<?php

namespace App\Http\Controllers;

use App\Models\Stunting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StuntingRequest;
use App\Http\Requests\StoreStuntingRequest;
use App\Http\Requests\UpdateStuntingRequest;
use Illuminate\Support\Carbon;

class StuntingController extends Controller
{
    // INDEX: daftar + filter server-side
    public function index(Request $req)
    {
        $q       = trim((string) $req->input('q', ''));
        $sev     = $req->input('severity'); // high|medium|low|'' 
        $periodM = $req->input('period');   // 'YYYY-MM' atau null
        $perPage = (int) $req->input('per_page', 20);

        // Rate expression (hindari div 0)
        $rateExpr = "(CASE WHEN popuLasi = 0 THEN 0 ELSE (kasus * 100.0) / populasi END)";

        $base = Stunting::query()->select('stuntings.*');

        if ($periodM) {
            // Jika user pilih periode → filter tepat bulan tsb (pakai tgl 1)
            try {
                $periodDate = Carbon::parse($periodM.'-01')->startOfMonth()->format('Y-m-d');
            } catch (\Throwable $e) {
                $periodDate = null;
            }
            if ($periodDate) {
                $base->whereDate('period', '=', $periodDate);
            }
        } else {
            // DEFAULT: ambil data TERBARU per desa
            // Join ke subquery (desa, max(period))
            $latestSub = DB::table('stuntings')
                ->select('desa', DB::raw('MAX(period) as max_period'))
                ->groupBy('desa');

            $base->joinSub($latestSub, 'm', function ($join) {
                $join->on('stuntings.desa', '=', 'm.desa')
                     ->on('stuntings.period', '=', 'm.max_period');
            });
        }

        // Search desa
        if ($q !== '') {
            $base->where('desa', 'LIKE', '%'.$q.'%');
        }

        // Filter severity (server-side menggunakan rateExpr)
        if ($sev === 'high') {
            $base->whereRaw("$rateExpr > 20");
        } elseif ($sev === 'medium') {
            $base->whereRaw("$rateExpr >= 10 AND $rateExpr <= 20");
        } elseif ($sev === 'low') {
            $base->whereRaw("$rateExpr < 10");
        }

        // Urutkan: terbaru dulu kalau user pilih periode (semua sama), tetap rapih per desa
        $rows = $base->orderBy('desa')->paginate($perPage)->withQueryString();

        return view('stunting.index', [
            'rows'   => $rows,
            'q'      => $q,
            'sev'    => $sev,
            'period' => $periodM, // untuk nge-set value input month
        ]);
    }

    public function create() {
        $defaultPeriod = now()->subMonth()->startOfMonth()->format('Y-m'); // YYYY-MM
        return view('stunting.create', compact('defaultPeriod'));
    }

    public function store(StoreStuntingRequest $request) {
        $data = $request->validated();
        $data['period'] = Carbon::createFromFormat('Y-m', $data['period'])->startOfMonth();
        Stunting::create($data);
        return redirect()->route('stunting.index')->with('ok','Data berhasil ditambahkan.');
    }

    public function edit(Stunting $stunting) {
        return view('stunting.edit', compact('stunting'));
    }

    public function update(StuntingRequest $request, Stunting $stunting)
    {
        $data = $request->validated();
        $newPeriod = Carbon::parse($data['period'])->startOfMonth();

        // Jika period berubah → buat BARU (append), data lama dibiarkan (riwayat otomatis)
        if ($newPeriod->ne($stunting->period)) {
            Stunting::create([
                'desa'     => $data['desa'],
                'kasus'    => $data['kasus'],
                'populasi' => $data['populasi'],
                'period'   => $newPeriod,
            ]);
            return redirect()->route('stunting.index')
                ->with('ok','Periode berubah → data bulan baru dibuat. Data lama tetap disimpan.');
        }

        // Period sama → update baris ini
        $stunting->update($data);
        return redirect()->route('stunting.index')->with('ok','Data diperbarui.');
    }

    public function destroy(Stunting $stunting) {
        $stunting->delete();
        return back()->with('ok','Data dihapus.');
    }

    public function chartData(Request $request)
    {
        // Ambil periode (YYYY-MM) atau pakai periode global terakhir
        $periodStr = $request->string('period')->toString();
        if ($periodStr) {
            $period = Carbon::createFromFormat('Y-m', $periodStr)->startOfMonth();
        } else {
            $max = Stunting::max('period');               // contoh: '2025-09-01'
            $period = $max ? Carbon::parse($max) : now()->startOfMonth();
        }

        // --- RANKING (horizontal bar) untuk periode terpilih ---
        $rankingRows = Stunting::query()
            ->whereDate('period', $period->toDateString())
            ->select(['desa','kasus','populasi'])
            ->get();

        // Hitung rate per desa dan urutkan desc
        $ranking = $rankingRows->map(function($r){
                $rate = $r->populasi > 0 ? round($r->kasus / $r->populasi * 100, 1) : 0;
                return ['desa'=>$r->desa, 'rate'=>$rate];
            })
            ->sortByDesc('rate')
            ->values();

        // --- TREND (line) 12 bulan ke belakang (agregat seluruh desa) ---
        $trendRows = Stunting::query()
            ->whereDate('period', '<=', $period->toDateString())
            ->selectRaw("DATE_FORMAT(period, '%Y-%m') as ym, SUM(kasus) as kasus, SUM(populasi) as pop")
            ->groupBy('ym')
            ->orderBy('ym', 'desc')
            ->limit(12)                      // ambil 12 bulan terakhir
            ->get()
            ->reverse()                      // urut naik (lama -> baru)
            ->values();

        $periods = $trendRows->pluck('ym');  // ['2024-10','2024-11',...]
        $trend   = $trendRows->map(fn($r) => $r->pop > 0 ? round($r->kasus / $r->pop * 100, 1) : 0);

        return response()->json([
            'period'  => $period->format('Y-m'),
            'ranking' => $ranking,                 // [{desa:'Pangalengan', rate:12.7}, ...]
            'periods' => $periods,                 // untuk sumbu-X line chart
            'trend'   => $trend,                   // [12.1, 11.7, ...]
        ]);
    }
}
