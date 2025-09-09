<?php

namespace App\Http\Controllers;

use App\Models\Stunting;
use App\Http\Requests\StoreStuntingRequest;
use App\Http\Requests\UpdateStuntingRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StuntingController extends Controller
{
    // INDEX: daftar + filter server-side
    public function index(Request $request) {
        $q    = $request->query('q');
        $sev  = $request->query('severity');
        $from = $request->query('from');  // YYYY-MM
        $to   = $request->query('to');    // YYYY-MM

        $rows = Stunting::query()
            ->search($q)
            ->severity($sev)
            ->periodBetween($from, $to)
            ->orderByDesc('period')
            ->orderBy('desa')
            ->paginate(20)
            ->withQueryString();

        return view('stunting.index', compact('rows','q','sev','from','to'));
    }

    public function create() {
    // default: bulan sebelumnya (YYYY-MM)
        $defaultPeriod = Carbon::now('Asia/Jakarta')
            ->subMonthNoOverflow()
            ->format('Y-m');
        
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

    public function update(UpdateStuntingRequest $request, Stunting $stunting) {
        $data = $request->validated();
        $data['period'] = Carbon::createFromFormat('Y-m', $data['period'])->startOfMonth();
        $stunting->update($data);
        return redirect()->route('stunting.index')->with('ok','Data berhasil diubah.');
    }

    public function destroy(Stunting $stunting) {
        $stunting->delete();
        return back()->with('ok','Data dihapus.');
    }
}
