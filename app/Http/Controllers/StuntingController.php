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

        // sorting params
        $sort = $req->input('sort', 'desa');             // desa|kasus|populasi|rate
        $dir  = strtolower($req->input('dir', 'asc'));   // asc|desc
        $dir  = in_array($dir, ['asc','desc'], true) ? $dir : 'asc';

        // amankan kolom sort
        $allowedSorts = ['desa','kasus','populasi','rate'];
        if (!in_array($sort, $allowedSorts, true)) $sort = 'desa';

        $rateExpr = "(CASE WHEN stuntings.populasi = 0 THEN 0 ELSE (stuntings.kasus * 100.0) / stuntings.populasi END)";
        $base = Stunting::query()->select('stuntings.*');

        // filter periode
        if ($periodM) {
            try {
                $periodDate = Carbon::parse($periodM.'-01')->startOfMonth()->format('Y-m-d');
            } catch (\Throwable $e) {
                $periodDate = null;
            }
            if ($periodDate) {
                $base->whereDate('stuntings.period', '=', $periodDate);
            }
        } else {
            // latest per desa
            $latestSub = DB::table('stuntings')
                ->select('desa', DB::raw('MAX(period) as max_period'))
                ->groupBy('desa');

            $base->joinSub($latestSub, 'm', function ($join) {
                $join->on('stuntings.desa', '=', 'm.desa')
                     ->on('stuntings.period', '=', 'm.max_period');
            });
        }

        // filter q
        if ($q !== '') {
            $base->where('stuntings.desa', 'LIKE', '%'.$q.'%');
        }

        // filter severity
        if ($sev === 'high') {
            $base->whereRaw("$rateExpr > 20");
        } elseif ($sev === 'medium') {
            $base->whereRaw("$rateExpr BETWEEN 10 AND 20");
        } elseif ($sev === 'low') {
            $base->whereRaw("$rateExpr < 10");
        }

        // apply sort
        if ($sort === 'rate') {
            $base->orderByRaw("$rateExpr $dir")->orderBy('stuntings.desa', 'asc');
        } else {
            $base->orderBy("stuntings.$sort", $dir)->orderBy('stuntings.desa', 'asc');
        }

        $rows = $base->paginate($perPage)->withQueryString();

        // label periode
        $periodLabel = $periodM ? Carbon::createFromFormat('Y-m', $periodM)->isoFormat("MMM 'YY") : null;

        $maxPeriodRaw = Stunting::max('period');
        $displayPeriodLabel = $maxPeriodRaw ? Carbon::parse($maxPeriodRaw)->isoFormat("MMM 'YY") : null;

        return view('stunting.index', [
            'rows'               => $rows,
            'q'                  => $q,
            'sev'                => $sev,
            'period'             => $periodM,
            'periodLabel'        => $periodLabel,
            'displayPeriodLabel' => $displayPeriodLabel,
            'sort'               => $sort,
            'dir'                => $dir,
        ]);
    }

    public function create() {
        $defaultPeriod = now()->subMonth()->startOfMonth()->format('Y-m');
        $desaOptions = array_keys(config('desa_coords', []));
        sort($desaOptions);
        return view('stunting.create', compact('defaultPeriod', 'desaOptions'));
    }

    public function store(StoreStuntingRequest $request) {
        $data = $request->validated();
        Stunting::create($data);
        return redirect()->route('stunting.index')->with('ok','Data berhasil ditambahkan.');
    }

    public function edit(Stunting $stunting) {
        $desaOptions = array_keys(config('desa_coords', []));
        sort($desaOptions);
        $lockDesa = true;
        return view('stunting.edit', compact('stunting', 'desaOptions'));
    }

    public function update(StuntingRequest $request, Stunting $stunting)
    {
        $data = $request->validated();
        $newPeriod = Carbon::parse($data['period'])->startOfMonth();

        // period berubah → buat baris baru
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

        // period sama → update baris ini
        $stunting->update($data);
        return redirect()->route('stunting.index')->with('ok','Data diperbarui.');
    }

    public function destroy(Stunting $stunting) {
        $stunting->delete();
        return back()->with('ok','Data dihapus.');
    }

    /**
     * Endpoint data Chart untuk tab "Chart" di Stunting:
     * - ranking per-desa (periode terpilih)
     * - trend 12 bulan (rata-rata tertimbang: Σkasus/Σpopulasi×100)
     */
    public function chartData(Request $request)
    {
        // anchor periode
        $periodStr = $request->string('period')->toString();
        if ($periodStr) {
            $period = Carbon::createFromFormat('Y-m', $periodStr)->startOfMonth();
        } else {
            $max = Stunting::max('period');
            $period = $max ? Carbon::parse($max)->startOfMonth() : now()->startOfMonth();
        }

        // RANKING periode terpilih
        $rankingRows = Stunting::query()
            ->whereDate('period', $period->toDateString())
            ->select(['desa','kasus','populasi'])
            ->get();

        $ranking = $rankingRows->map(function($r){
                $rate = $r->populasi > 0 ? round(($r->kasus / $r->populasi) * 100, 1) : 0.0;
                return ['desa'=>$r->desa, 'rate'=>$rate];
            })
            ->sortByDesc('rate')
            ->take(25) // Top 25 supaya ringan (boleh ubah)
            ->values();

        // TREND 12 bulan (tertimbang)
        $trendRows = Stunting::query()
            ->whereDate('period', '<=', $period->toDateString())
            ->selectRaw("DATE_FORMAT(period, '%Y-%m') as ym, SUM(kasus) as kasus, SUM(populasi) as pop")
            ->groupBy('ym')
            ->orderBy('ym', 'desc')
            ->limit(12)
            ->get()
            ->reverse()
            ->values();

        $periods = $trendRows->pluck('ym');  // ['2024-09',...]
        $trend   = $trendRows->map(fn($r) => $r->pop > 0 ? round(($r->kasus / $r->pop) * 100, 1) : 0.0);

        return response()->json([
            'period'  => $period->format('Y-m'),
            'ranking' => $ranking,
            'periods' => $periods,
            'trend'   => $trend,
        ]);
    }
}
