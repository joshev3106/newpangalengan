<?php

namespace App\Http\Controllers;

use App\Models\Stunting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class HotspotController extends Controller
{
    // Halaman (publik)
    public function index(Request $request)
    {
        [$periodRaw, $data] = $this->buildDataset($request);

        // Label periode
        $periodLabel = $periodRaw ? Carbon::createFromFormat('Y-m', $periodRaw)->isoFormat("MMM 'YY") : null;
        $maxPeriodRaw = Stunting::max('period');
        $displayPeriodLabel = $maxPeriodRaw ? Carbon::parse($maxPeriodRaw)->isoFormat("MMM 'YY") : null;

        // === NEW: Tab view (table|map) ===
        $currentView = $request->query('view', 'table');
        if (!in_array($currentView, ['table','map'], true)) $currentView = 'table';

        // === NEW: Sorting (desa|cases|rate|confidence) & dir (asc|desc) ===
        $sort = $request->query('sort', 'desa');
        $dir  = strtolower($request->query('dir', 'asc'));
        $dir  = in_array($dir, ['asc','desc'], true) ? $dir : 'asc';

        $allowedSorts = ['desa','cases','rate','confidence'];
        if (!in_array($sort, $allowedSorts, true)) $sort = 'desa';

        // Terapkan sort ke collection $data (collection of arrays)
        $data = $data->sortBy(function ($item) use ($sort) {
            if ($sort === 'desa') {
                return mb_strtolower($item['desa'] ?? '');
            }
            // numeric
            return $item[$sort] ?? 0;
        }, SORT_REGULAR, $dir === 'desc')->values();

        // Stats
        $stats = [
            'high'   => $data->where('confidence', 99)->count(),
            'medium' => $data->where('confidence', 95)->count(),
            'low'    => $data->where('confidence', 90)->count(),
            'not'    => $data->where('confidence', 0)->count(),
            'total'  => $data->count(),
        ];

        // Pagination untuk TAB TABLE saja (dataset lengkap tetap dipakai untuk Map)
        $perPage  = (int) $request->query('per_page', 20);
        $perPage  = $perPage > 0 ? $perPage : 20;
        $page     = max(1, (int) $request->query('page', 1));

        $items    = $data->forPage($page, $perPage)->values();
        $hotspots = new LengthAwarePaginator(
            $items,
            $data->count(),
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        // Dataset penuh untuk peta (semua titik, tidak terbatasi pagination)
        $datasetAll = $data->values();

        return view('hotspot.index', compact(
            'hotspots',
            'datasetAll',
            'stats',
            'periodLabel',
            'displayPeriodLabel',
            'currentView',
            'sort',
            'dir'
        ));
    }

    // JSON publik (kalau butuh fetch via JS)
    public function data(Request $request)
    {
        [, $data] = $this->buildDataset($request);
        return response()->json($data->values());
    }

    /**
     * Build dataset dari tabel stuntings:
     * - Jika ada ?period=YYYY-MM / YYYY-MM-DD → ambil periode itu.
     * - Jika tidak, ambil record terbaru per desa (MAX(period)).
     * - Hitung rate => severity => confidence.
     * - Tempel lat/lng dari config('desa_coords').
     *
     * @return array{0:?string,1:Collection<int,array<string,mixed>>} [periodUntukView, data]
     */
    private function buildDataset(Request $request): array
    {
        $coords = config('desa_coords', []);

        // Normalisasi period (?period=YYYY-MM atau YYYY-MM-DD)
        $periodParam = trim((string) $request->query('period', ''));
        $normalized  = null;  // YYYY-MM-01
        if ($periodParam !== '') {
            if (preg_match('/^\d{4}-\d{2}$/', $periodParam)) {
                $normalized = $periodParam . '-01';
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $periodParam)) {
                $normalized = $periodParam;
            } else {
                try {
                    $normalized = Carbon::parse($periodParam)->startOfMonth()->toDateString();
                } catch (\Throwable $e) {
                    $normalized = null;
                }
            }
        }

        if ($normalized) {
            // Filter periode tertentu
            $rows = Stunting::whereDate('period', $normalized)
                ->orderBy('desa')
                ->get();
        } else {
            // Terbaru per desa (MAX(period))
            $latest = Stunting::select('desa')
                ->selectRaw('MAX(period) as last_period')
                ->groupBy('desa');

            $rows = Stunting::joinSub($latest, 'latest', function ($join) {
                    $join->on('stuntings.desa', '=', 'latest.desa')
                         ->on('stuntings.period', '=', 'latest.last_period');
                })
                ->orderBy('stuntings.desa')
                ->get(['stuntings.*']);
        }

        $collection = $rows->map(function (Stunting $s) use ($coords) {
            $rate = $s->populasi > 0 ? round(($s->kasus / $s->populasi) * 100, 1) : 0.0;

            // severity → confidence (proxy)
            $severity   = $rate > 20 ? 'high' : ($rate >= 10 ? 'medium' : 'low');
            $confidence = $rate == 0 ? 0 : ($severity === 'high' ? 99 : ($severity === 'medium' ? 95 : 90));

            $point = $coords[$s->desa] ?? null;

            // pastikan period string "Y-m" utk view/JS
            $periodStr = $s->period instanceof \Carbon\CarbonInterface
                ? $s->period->format('Y-m')
                : Carbon::parse($s->period)->format('Y-m');

            return [
                'id'         => $s->id,
                'desa'       => $s->desa,
                'name'       => "Cluster - {$s->desa}",
                'lat'        => $point['lat'] ?? null,
                'lng'        => $point['lng'] ?? null,
                'population' => (int) $s->populasi,
                'cases'      => (int) $s->kasus,
                'rate'       => $rate,
                'severity'   => $severity,
                'confidence' => $confidence,
                'period'     => $periodStr,
            ];
        });

        // Nilai period utk info bar di view (YYYY-MM) atau null (data terbaru)
        $periodForView = $normalized ? substr($normalized, 0, 7) : null;

        return [$periodForView, $collection];
    }
}
