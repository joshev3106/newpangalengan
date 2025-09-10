<?php

namespace App\Http\Controllers;

use App\Models\Stunting;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class HotspotController extends Controller
{
    // Halaman (publik)
        public function index(Request $request)
    {
        $data = $this->buildDataset($request); // Collection of arrays

        $stats = [
            'high'   => $data->where('confidence', 99)->count(),
            'medium' => $data->where('confidence', 95)->count(),
            'low'    => $data->where('confidence', 90)->count(),
            'not'    => $data->where('confidence', 0)->count(),
            'total'  => $data->count(),
        ];

        $perPage = 20;
        $page    = $request->integer('page', 1);
        $items   = $data->forPage($page, $perPage)->values();
        $hotspots = new LengthAwarePaginator(
            $items, $data->count(), $perPage, $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('hotspot.index', compact('hotspots', 'stats'));
    }

    // JSON publik (kalau butuh fetch via JS)
    public function data(Request $request)
    {
        return response()->json($this->buildDataset($request)->values());
    }

    /**
     * Build dataset dari tabel stuntings:
     * - record terbaru per desa (MAX(period)) atau filter ?period=YYYY-MM
     * - hitung rate => severity => confidence
     * - tempel lat/lng dari config/desa_coords.php
     */
    private function buildDataset(Request $request): Collection
    {
        $coords = config('desa_coords', []);
        $periodQ = $request->string('period')->toString(); // opsional ?period=2025-09

        if ($periodQ) {
            $rows = Stunting::where('period', $periodQ.'-01')->get();
        } else {
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

        return $rows->map(function (Stunting $s) use ($coords) {
            $rate = $s->populasi > 0 ? round(($s->kasus / $s->populasi) * 100, 1) : 0.0;

            // severity → confidence (proxy)
            $severity   = $rate > 20 ? 'high' : ($rate >= 10 ? 'medium' : 'low');
            $confidence = match ($severity) {
                'high'   => 99,
                'medium' => 95,
                default  => ($rate > 0 ? 90 : 0),
            };

            $point = $coords[$s->desa] ?? null;

            return [
                'id'         => $s->id,
                'desa'       => $s->desa,
                'name'       => "Cluster - {$s->desa}",
                'lat'        => $point['lat'] ?? null,
                'lng'        => $point['lng'] ?? null,
                'population' => (int) $s->populasi,   // <= penting buat popup
                'cases'      => (int) $s->kasus,
                'rate'       => $rate,
                'severity'   => $severity,
                'confidence' => $confidence,
                'period'     => optional($s->period)->format('Y-m'),
            ];
        });
    }

    // Catatan: kalau kamu masih punya method store/update/destroy dari CRUD lama, biarkan saja
    // atau nonaktifkan jika kini semua dihitung dari tabel 'stuntings'.

    // // FORM CREATE
    // public function create()
    // {
    //     return view('hotspot.create');
    // }

    // // SIMPAN
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'name'       => ['required','string','max:150'],
    //         'lat'        => ['required','numeric','between:-90,90'],
    //         'lng'        => ['required','numeric','between:-180,180'],
    //         'confidence' => ['required','integer','in:0,90,95,99'],
    //         'cases'      => ['required','integer','min:0'],
    //     ]);

    //     Hotspot::create($data);
    //     return redirect()->route('hotspot.index')->with('ok','Hotspot berhasil ditambahkan');
    // }

    // // FORM EDIT
    // public function edit(Hotspot $hotspot)
    // {
    //     return view('hotspot.edit', compact('hotspot'));
    // }

    // // UPDATE
    // public function update(Request $request, Hotspot $hotspot)
    // {
    //     $data = $request->validate([
    //         'name'       => ['required','string','max:150'],
    //         'lat'        => ['required','numeric','between:-90,90'],
    //         'lng'        => ['required','numeric','between:-180,180'],
    //         'confidence' => ['required','integer','in:0,90,95,99'],
    //         'cases'      => ['required','integer','min:0'],
    //     ]);

    //     $hotspot->update($data);
    //     return redirect()->route('hotspot.index')->with('ok','Hotspot berhasil diupdate');
    // }

    // // HAPUS
    // public function destroy(Hotspot $hotspot)
    // {
    //     $hotspot->delete();
    //     return back()->with('ok','Hotspot dihapus');
    // }
}
