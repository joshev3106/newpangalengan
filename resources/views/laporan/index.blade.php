{{-- resources/views/laporan/index.blade.php --}}
<x-layout>
  @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
    <style>
      .table-scroll{overflow:auto}
      th.sticky{position:sticky;top:0;background:#fff;z-index:5}
      #map-thematic{height:420px;width:100%}
      @media(min-width:1024px){#map-thematic{height:480px}}
    </style>
  @endpush

  @php
    // ========= Fallback dari controller (boleh dihapus bila sudah disupply controller) =========
    $periodType   = $periodType   ?? request('period_type', 'monthly');  // monthly|quarterly|yearly
    $periodValue  = $periodValue  ?? request('period_value');             // 'YYYY-MM' | 'YYYY-Qx' | 'YYYY'
    $desaList     = $desaList     ?? array_keys(config('desa_coords', []));
    sort($desaList);
    $selectedDesa = $selectedDesa ?? request('desa', ''); // '' = semua desa
    $reportKind   = $reportKind   ?? request('kind', 'comprehensive'); // stunting|hotspot|coverage|comprehensive
    $template     = $template     ?? request('template', 'executive');  // executive|detailed|comparison
    $kpi          = $kpi ?? [
      'total_desa' => 0, 'avg_rate' => null, 'hotspot' => ['99'=>0,'95'=>0,'90'=>0],
      'coverage_avg'=>null,'updated'=>($displayPeriodLabel ?? '-')
    ];
    $history      = $history ?? []; // [{id,title,when,kind,period,template}, ...]
    $bookmarks    = $bookmarks ?? []; // [{id,name,params}, ...]
    $comparisonMeta = $comparisonMeta ?? ['basis'=>'MoM','periodA'=>'-','periodB'=>'-','delta'=>0.0];

    // Data untuk chart & peta (dummy fallback)
    $ranking = $ranking ?? []; // [['desa'=>'X','rate'=>12.3], ...]
    $trend   = $trend   ?? ['labels'=>[], 'values'=>[]]; // 12 bulan
    $mapData = $mapData ?? []; // [['desa'=>'','lat'=>..,'lng'=>..,'rate'=>..,'confidence'=>..], ...]
    $fmtInt  = fn($n) => number_format((int)($n??0));
    $fmtPct  = fn($v) => is_null($v) ? '—' : number_format((float)$v, 1) . '%';
  @endphp

  {{-- =================== HEADER =================== --}}
  <header class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-gradient-to-tr from-red-700 via-red-600 to-rose-600"></div>
    <div class="absolute -top-24 -right-24 w-[42rem] h-[42rem] rounded-full bg-white/10 blur-3xl -z-10"></div>

    <div class="max-w-7xl mx-auto px-6 py-8 text-white">
      <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight">Laporan</h1>
          <p class="text-red-50/90 text-sm">Generator & arsip laporan untuk dokumentasi, evaluasi, dan pelaporan instansi.</p>
        </div>

        {{-- Aksi cepat export --}}
        <div class="flex flex-wrap gap-2">
          <form method="POST" action="{{ route('laporan.export') }}" class="contents">
            @csrf
            <input type="hidden" name="format" value="pdf">
            <button class="px-3 py-2 rounded-lg bg-white text-red-700 font-semibold hover:bg-red-50 text-sm">Export PDF</button>
          </form>
          <form method="POST" action="{{ route('laporan.export') }}" class="contents">
            @csrf
            <input type="hidden" name="format" value="xlsx">
            <button class="px-3 py-2 rounded-lg bg-white/90 text-red-700 font-semibold hover:bg-white text-sm">Export Excel</button>
          </form>
          <form method="POST" action="{{ route('laporan.export') }}" class="contents">
            @csrf
            <input type="hidden" name="format" value="pptx">
            <button class="px-3 py-2 rounded-lg bg-white/90 text-red-700 font-semibold hover:bg-white text-sm">Export PowerPoint</button>
          </form>
        </div>
      </div>
    </div>
  </header>

  <x-navbar></x-navbar>

  <div class="max-w-7xl mx-auto px-6 py-8">

    {{-- =================== GENERATOR LAPORAN =================== --}}
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5 mb-6">
      <div class="flex items-start justify-between gap-3">
        <div>
          <h2 class="text-lg font-semibold text-gray-800">Generator Laporan</h2>
          <p class="text-sm text-gray-500">Pilih periode, wilayah, jenis laporan & template.</p>
        </div>
        <form method="POST" action="{{ route('laporan.bookmark') }}" class="hidden md:block">
          @csrf
          <input type="hidden" name="name" value="">
          <button type="submit" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Bookmark</button>
        </form>
      </div>

      <form id="reportFilter" method="GET" action="{{ route('laporan.index') }}" class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        {{-- Periode --}}
        <div class="rounded-xl ring-1 ring-gray-200 p-3">
          <label class="text-xs font-medium text-gray-500">Periode</label>
          <div class="mt-2 space-y-2">
            <select name="period_type" id="period_type" class="w-full rounded-lg border-gray-300">
              <option value="monthly"   @selected($periodType==='monthly')>Bulanan</option>
              <option value="quarterly" @selected($periodType==='quarterly')>Triwulan</option>
              <option value="yearly"    @selected($periodType==='yearly')>Tahunan</option>
            </select>
            {{-- input dinamis --}}
            <div id="period_monthly"   class="{{ $periodType==='monthly'?'':'hidden' }}">
              <input type="month" name="period_value_monthly" value="{{ $periodType==='monthly' ? $periodValue : '' }}" class="w-full rounded-lg border-gray-300">
            </div>
            <div id="period_quarterly" class="{{ $periodType==='quarterly'?'':'hidden' }} grid grid-cols-2 gap-2">
              <select name="period_value_quarter" class="rounded-lg border-gray-300">
                <option value="Q1" @selected(str_ends_with($periodValue ?? '','Q1'))>Triwulan 1</option>
                <option value="Q2" @selected(str_ends_with($periodValue ?? '','Q2'))>Triwulan 2</option>
                <option value="Q3" @selected(str_ends_with($periodValue ?? '','Q3'))>Triwulan 3</option>
                <option value="Q4" @selected(str_ends_with($periodValue ?? '','Q4'))>Triwulan 4</option>
              </select>
              <input type="number" min="2000" max="2100" name="period_value_year_q" value="{{ preg_replace('/\D/','',$periodValue ?? '') }}" placeholder="Tahun" class="rounded-lg border-gray-300">
            </div>
            <div id="period_yearly"    class="{{ $periodType==='yearly'?'':'hidden' }}">
              <input type="number" min="2000" max="2100" name="period_value_year" value="{{ $periodType==='yearly' ? $periodValue : '' }}" class="w-full rounded-lg border-gray-300" placeholder="Tahun">
            </div>
          </div>
        </div>

        {{-- Wilayah --}}
        <div class="rounded-xl ring-1 ring-gray-200 p-3">
          <label class="text-xs font-medium text-gray-500">Wilayah</label>
          <div class="mt-2">
            <input type="text" list="desaOptions" name="desa" value="{{ $selectedDesa }}" placeholder="Semua desa / ketik nama" class="w-full rounded-lg border-gray-300">
            <datalist id="desaOptions">
              @foreach($desaList as $d) <option value="{{ $d }}">{{ $d }}</option> @endforeach
            </datalist>
          </div>
          <p class="text-[11px] text-gray-400 mt-1">Kosongkan untuk semua desa.</p>
        </div>

        {{-- Jenis Laporan --}}
        <div class="rounded-xl ring-1 ring-gray-200 p-3">
          <label class="text-xs font-medium text-gray-500">Jenis Laporan</label>
          <select name="kind" class="mt-2 w-full rounded-lg border-gray-300">
            <option value="comprehensive" @selected($reportKind==='comprehensive')>Komprehensif</option>
            <option value="stunting"      @selected($reportKind==='stunting')>Stunting</option>
            <option value="hotspot"       @selected($reportKind==='hotspot')>Hotspot</option>
            <option value="coverage"      @selected($reportKind==='coverage')>Cakupan Layanan</option>
          </select>
          <div class="mt-2">
            <label class="text-xs font-medium text-gray-500">Template</label>
            <select name="template" class="mt-2 w-full rounded-lg border-gray-300">
              <option value="executive"  @selected($template==='executive')>Executive Summary</option>
              <option value="detailed"   @selected($template==='detailed')>Detailed Report</option>
              <option value="comparison" @selected($template==='comparison')>Comparison Report</option>
            </select>
          </div>
        </div>

        {{-- Tombol --}}
        <div class="rounded-xl ring-1 ring-gray-200 p-3 flex flex-col justify-between">
          <div class="text-xs font-medium text-gray-500">Aksi</div>
          <div class="mt-2 grid grid-cols-2 gap-2">
            <button class="px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-500">Terapkan</button>
            <a href="{{ route('laporan.index') }}" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-center">Reset</a>
          </div>
          <div class="mt-2 text-[11px] text-gray-400">Gunakan “Bookmark” untuk menyimpan konfigurasi favorit.</div>
        </div>
      </form>
    </div>

    {{-- =================== LAPORAN EKSEKUTIF =================== --}}
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5 mb-6">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800">Laporan Eksekutif</h2>
        <div class="text-sm text-gray-500">Update terakhir: <span class="font-medium">{{ $kpi['updated'] }}</span></div>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
        <div class="bg-white rounded-2xl p-4 shadow-sm ring-1 ring-gray-100 border-t-4 border-red-600">
          <div class="text-sm text-gray-500">Rata-rata Stunting</div>
          <div class="text-2xl font-bold">{{ $fmtPct($kpi['avg_rate']) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm ring-1 ring-gray-100 border-t-4 border-orange-500">
          <div class="text-sm text-gray-500">Hotspot</div>
          <div class="text-sm text-gray-700 mt-1 space-y-1">
            <div class="flex items-center justify-between"><span class="inline-flex items-center gap-1"><i class="w-2.5 h-2.5 bg-red-600 inline-block rounded"></i>99%</span><span class="font-semibold">{{ $fmtInt($kpi['hotspot']['99'] ?? 0) }}</span></div>
            <div class="flex items-center justify-between"><span class="inline-flex items-center gap-1"><i class="w-2.5 h-2.5 bg-orange-600 inline-block rounded"></i>95%</span><span class="font-semibold">{{ $fmtInt($kpi['hotspot']['95'] ?? 0) }}</span></div>
            <div class="flex items-center justify-between"><span class="inline-flex items-center gap-1"><i class="w-2.5 h-2.5 bg-yellow-400 inline-block rounded"></i>90%</span><span class="font-semibold">{{ $fmtInt($kpi['hotspot']['90'] ?? 0) }}</span></div>
          </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm ring-1 ring-gray-100 border-t-4 border-yellow-500">
          <div class="text-sm text-gray-500">Rata-rata Cakupan</div>
          <div class="text-2xl font-bold">{{ $fmtPct($kpi['coverage_avg']) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm ring-1 ring-gray-100 border-t-4 border-gray-400">
          <div class="text-sm text-gray-500">Total Desa</div>
          <div class="text-2xl font-bold">{{ $fmtInt($kpi['total_desa']) }}</div>
        </div>
      </div>

      {{-- Highlight & Rekomendasi --}}
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-5">
        <div class="rounded-xl ring-1 ring-gray-200 p-4">
          <h3 class="font-semibold text-gray-800">Highlight & Pencapaian</h3>
          <ul class="list-disc list-inside text-sm text-gray-700 mt-2 space-y-1" id="highlightList">
            <li>—</li>
          </ul>
        </div>
        <div class="rounded-xl ring-1 ring-gray-200 p-4">
          <h3 class="font-semibold text-gray-800">Rekomendasi Strategis</h3>
          <ul class="list-disc list-inside text-sm text-gray-700 mt-2 space-y-1" id="rekomendasiList">
            <li>—</li>
          </ul>
        </div>
      </div>

      {{-- Perbandingan --}}
      <div class="rounded-xl ring-1 ring-gray-200 p-4 mt-4">
        <h3 class="font-semibold text-gray-800">Perbandingan Periode ({{ $comparisonMeta['basis'] ?? '—' }})</h3>
        <p class="text-sm text-gray-600 mt-1">Periode A: <b>{{ $comparisonMeta['periodA'] }}</b> vs Periode B: <b>{{ $comparisonMeta['periodB'] }}</b></p>
        <div class="mt-3 h-56 md:h-64"><canvas id="compareChart"></canvas></div>
      </div>
    </div>

    {{-- =================== DETAIL PER KATEGORI =================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
      {{-- STUNTING --}}
      <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-4">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-800">Laporan Stunting</h3>
          <a href="{{ route('stunting.index') }}" class="text-sm text-red-600 hover:underline">Buka Halaman</a>
        </div>
        <p class="text-sm text-gray-500 mb-2">Analisis per desa & tren historis.</p>

        <div class="h-56 md:h-64 mb-3"><canvas id="rankChart"></canvas></div>

        <div class="table-scroll">
          <table class="min-w-full text-left text-sm">
            <thead class="text-gray-600 border-b">
              <tr>
                <th class="sticky px-3 py-2 font-semibold uppercase tracking-wider">Desa</th>
                <th class="sticky px-3 py-2 font-semibold uppercase tracking-wider">Kasus</th>
                <th class="sticky px-3 py-2 font-semibold uppercase tracking-wider">Pop.</th>
                <th class="sticky px-3 py-2 font-semibold uppercase tracking-wider">Rate</th>
              </tr>
            </thead>
            <tbody class="divide-y" id="tblStunting">
              <tr><td colspan="4" class="px-3 py-4 text-center text-gray-400">—</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      {{-- HOTSPOT --}}
      <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-4">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-800">Laporan Hotspot</h3>
          <a href="{{ route('hotspot.index') }}" class="text-sm text-red-600 hover:underline">Buka Halaman</a>
        </div>
        <p class="text-sm text-gray-500 mb-2">Desa prioritas & perubahan status.</p>

        <div class="h-56 md:h-64 mb-3"><canvas id="hotspotTrendChart"></canvas></div>

        <div class="table-scroll">
          <table class="min-w-full text-left text-sm">
            <thead class="text-gray-600 border-b">
              <tr>
                <th class="sticky px-3 py-2 font-semibold uppercase tracking-wider">Desa</th>
                <th class="sticky px-3 py-2 font-semibold uppercase tracking-wider">Rate</th>
                <th class="sticky px-3 py-2 font-semibold uppercase tracking-wider">Conf.</th>
              </tr>
            </thead>
            <tbody class="divide-y" id="tblHotspot">
              <tr><td colspan="3" class="px-3 py-4 text-center text-gray-400">—</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      {{-- CAKUPAN --}}
      <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-4">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-800">Laporan Cakupan Layanan</h3>
          <a href="{{ route('wilayah.index') }}" class="text-sm text-red-600 hover:underline">Buka Halaman</a>
        </div>
        <p class="text-sm text-gray-500 mb-2">Efektivitas puskesmas & gap analysis.</p>

        <div class="h-56 md:h-64 mb-3"><canvas id="coverageChart"></canvas></div>

        <div class="table-scroll">
          <table class="min-w-full text-left text-sm">
            <thead class="text-gray-600 border-b">
              <tr>
                <th class="sticky px-3 py-2 font-semibold uppercase tracking-wider">Puskesmas</th>
                <th class="sticky px-3 py-2 font-semibold uppercase tracking-wider">Cakupan</th>
                <th class="sticky px-3 py-2 font-semibold uppercase tracking-wider">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y" id="tblCoverage">
              <tr><td colspan="3" class="px-3 py-4 text-center text-gray-400">—</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- =================== VISUALISASI & INFOGRAFIS =================== --}}
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5 mb-6">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800">Visualisasi & Infografis</h2>
        <div class="flex gap-2">
          <button id="btnExportPng" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Unduh Chart (PNG)</button>
          <button id="btnExportCsv" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Unduh Data (CSV)</button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
        <div class="rounded-xl ring-1 ring-gray-200 p-4">
          <h3 class="font-semibold text-gray-800">Dashboard Visual (Trend)</h3>
          <div class="h-64 md:h-72"><canvas id="trendChart"></canvas></div>
        </div>
        <div class="rounded-xl ring-1 ring-gray-200 p-4">
          <h3 class="font-semibold text-gray-800">Peta Tematik</h3>
          <div id="map-thematic" class="rounded-xl overflow-hidden ring-1 ring-gray-100"></div>
        </div>
      </div>

      <div class="rounded-xl ring-1 ring-gray-200 p-4 mt-4">
        <h3 class="font-semibold text-gray-800">Comparison Chart</h3>
        <div class="h-64 md:h-72"><canvas id="compareByDesaChart"></canvas></div>
      </div>
    </div>

    {{-- =================== EXPORT & PENJADWALAN =================== --}}
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5 mb-6">
      <h2 class="text-lg font-semibold text-gray-800 mb-3">Export & Sharing</h2>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="rounded-xl ring-1 ring-gray-200 p-4">
          <h3 class="font-semibold">Format Export</h3>
          <p class="text-sm text-gray-500">Unduh laporan dalam format presentasi/analisis.</p>
          <div class="mt-3 grid grid-cols-3 gap-2">
            <form method="POST" action="{{ route('laporan.export') }}">@csrf<input type="hidden" name="format" value="pdf"><button class="px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-500 w-full">PDF</button></form>
            <form method="POST" action="{{ route('laporan.export') }}">@csrf<input type="hidden" name="format" value="xlsx"><button class="px-3 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800 w-full">Excel</button></form>
            <form method="POST" action="{{ route('laporan.export') }}">@csrf<input type="hidden" name="format" value="pptx"><button class="px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500 w-full">PPTX</button></form>
          </div>
        </div>

        <div class="rounded-xl ring-1 ring-gray-200 p-4">
          <h3 class="font-semibold">Laporan Terjadwal</h3>
          <p class="text-sm text-gray-500">Otomatis kirim laporan per periode.</p>
          <form class="mt-3 grid grid-cols-2 gap-2" method="POST" action="{{ route('laporan.schedule') }}">
            @csrf
            <select name="freq" class="rounded-lg border-gray-300">
              <option value="monthly">Bulanan</option>
              <option value="quarterly">Triwulan</option>
            </select>
            <input name="email" type="email" placeholder="email@instansi.go.id" class="rounded-lg border-gray-300">
            <button class="col-span-2 px-3 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800">Aktifkan</button>
          </form>
        </div>

        <div class="rounded-xl ring-1 ring-gray-200 p-4">
          <h3 class="font-semibold">Template Kustomisasi</h3>
          <p class="text-sm text-gray-500">Sesuaikan cover, logo, dan gaya untuk instansi.</p>
          <div class="mt-3 flex gap-2">
            <a href="{{ route('laporan.template') }}" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">Kelola Template</a>
            <a href="{{ route('laporan.branding') }}" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">Branding</a>
          </div>
        </div>
      </div>
    </div>

    {{-- =================== ARSIP & BOOKMARK =================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5 lg:col-span-2">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Arsip Laporan</h2>
        <div class="table-scroll">
          <table class="min-w-full text-left text-sm">
            <thead class="text-gray-600 border-b">
              <tr>
                <th class="sticky px-4 py-3 font-semibold uppercase tracking-wider">Judul</th>
                <th class="sticky px-4 py-3 font-semibold uppercase tracking-wider">Jenis</th>
                <th class="sticky px-4 py-3 font-semibold uppercase tracking-wider">Periode</th>
                <th class="sticky px-4 py-3 font-semibold uppercase tracking-wider">Template</th>
                <th class="sticky px-4 py-3 font-semibold uppercase tracking-wider">Tanggal</th>
                <th class="sticky px-4 py-3 font-semibold uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              @forelse($history as $h)
                <tr>
                  <td class="px-4 py-3">{{ $h['title'] ?? '-' }}</td>
                  <td class="px-4 py-3 capitalize">{{ $h['kind'] ?? '-' }}</td>
                  <td class="px-4 py-3">{{ $h['period'] ?? '-' }}</td>
                  <td class="px-4 py-3 capitalize">{{ $h['template'] ?? '-' }}</td>
                  <td class="px-4 py-3">{{ $h['when'] ?? '-' }}</td>
                  <td class="px-4 py-3">
                    <a href="{{ route('laporan.show', $h['id'] ?? 0) }}" class="text-blue-600 hover:underline">Buka</a>
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Belum ada arsip.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Bookmark Laporan</h2>
        <ul class="space-y-2">
          @forelse($bookmarks as $b)
            <li class="rounded-lg ring-1 ring-gray-200 p-3 flex items-center justify-between">
              <div>
                <div class="font-medium">{{ $b['name'] ?? '-' }}</div>
                <div class="text-xs text-gray-500">{{ json_encode($b['params'] ?? []) }}</div>
              </div>
              <a href="{{ route('laporan.apply-bookmark', $b['id'] ?? 0) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Gunakan</a>
            </li>
          @empty
            <li class="text-sm text-gray-400">Belum ada bookmark.</li>
          @endforelse
        </ul>
      </div>
    </div>

  </div> {{-- /container --}}

  @push('scripts')
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    {{-- Leaflet --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>

    <script>
      // ======= Utils =======
      const colorByRate = (v) => v > 20 ? '#dc2626' : (v >= 10 ? '#f97316' : '#16a34a');
      const pad5 = (v) => (v <= 0 ? 5 : Math.ceil((v * 1.15) / 5) * 5);

      // ======= Period Switcher (Generator) =======
      const periodTypeSel = document.getElementById('period_type');
      const m = document.getElementById('period_monthly');
      const q = document.getElementById('period_quarterly');
      const y = document.getElementById('period_yearly');
      function switchPeriodInput() {
        const t = periodTypeSel.value;
        m.classList.toggle('hidden', t!=='monthly');
        q.classList.toggle('hidden', t!=='quarterly');
        y.classList.toggle('hidden', t!=='yearly');
      }
      periodTypeSel?.addEventListener('change', switchPeriodInput);

      // ======= Data dari server (fallback jika kosong) =======
      const RANKING = @json($ranking);
      const TREND   = @json($trend);
      const MAPDATA = @json($mapData);
      const COMPARE = @json($comparisonMeta);

      // ======= Charts =======
      function buildRankChart() {
        const el = document.getElementById('rankChart');
        if (!el) return;
        const labels = (RANKING || []).map(r => r.desa);
        const data   = (RANKING || []).map(r => r.rate || 0);
        new Chart(el.getContext('2d'), {
          type: 'bar',
          data: { labels, datasets: [{ label:'Rate (%)', data, backgroundColor: data.map(colorByRate), borderWidth: 0 }] },
          options: {
            indexAxis:'y', responsive:true, maintainAspectRatio:false,
            scales:{ x:{ beginAtZero:true, suggestedMax: pad5(Math.max(0, ...data)), ticks:{ callback:v=>v+'%' } } },
            plugins:{ legend:{ display:false } }
          }
        });

        // Isi tabel stunting
        const tbody = document.getElementById('tblStunting');
        if (tbody && labels.length) {
          tbody.innerHTML = labels.map((d,i) => `
            <tr class="hover:bg-gray-50">
              <td class="px-3 py-2 font-medium text-gray-800">${d}</td>
              <td class="px-3 py-2">—</td>
              <td class="px-3 py-2">—</td>
              <td class="px-3 py-2"><span class="px-2 py-1 rounded-lg text-xs font-semibold" style="background:${colorByRate(data[i])};color:#fff">${(data[i]??0).toFixed(1)}%</span></td>
            </tr>`).join('');
        }
      }

      function buildHotspotTrend() {
        const el = document.getElementById('hotspotTrendChart');
        if (!el) return;
        const labels = TREND.labels || [];
        const values = (TREND.values || []).map(v => v*0.4); // placeholder transform utk hotspot
        new Chart(el.getContext('2d'), {
          type: 'line',
          data: { labels, datasets:[{ label:'Hotspot Index', data:values, tension:.1, fill:true, borderColor:'#ef4444', backgroundColor:'rgba(239,68,68,.1)' }]},
          options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{ y:{ beginAtZero:true } } }
        });

        const tbody = document.getElementById('tblHotspot');
        if (tbody && labels.length) {
          tbody.innerHTML = (RANKING||[]).slice(0,8).map(r => `
            <tr class="hover:bg-gray-50">
              <td class="px-3 py-2 font-medium text-gray-800">${r.desa}</td>
              <td class="px-3 py-2">${(r.rate??0).toFixed(1)}%</td>
              <td class="px-3 py-2"><span class="px-2 py-1 rounded-lg text-xs font-semibold ${r.rate>20?'bg-red-100 text-red-800':(r.rate>=10?'bg-orange-100 text-orange-800':'bg-yellow-100 text-yellow-800')}">${r.rate>20?'99%':(r.rate>=10?'95%':'90%')}</span></td>
            </tr>`).join('');
        }
      }

      function buildCoverageChart() {
        const el = document.getElementById('coverageChart');
        if (!el) return;
        // placeholder: derive from ranking
        const labels = (RANKING||[]).slice(0,8).map(r=>r.desa);
        const cov    = (RANKING||[]).slice(0,8).map(r=> Math.max(0, Math.min(100, 70 + (10 - r.rate))));
        new Chart(el.getContext('2d'), {
          type:'bar',
          data:{ labels, datasets:[{ label:'Cakupan (%)', data:cov, backgroundColor:'#111827' }]},
          options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{ y:{ beginAtZero:true, max:100, ticks:{callback:v=>v+'%'} } } }
        });

        const tbody = document.getElementById('tblCoverage');
        if (tbody && labels.length) {
          tbody.innerHTML = labels.map((d,i)=>`
            <tr class="hover:bg-gray-50">
              <td class="px-3 py-2 font-medium text-gray-800">PK ${d}</td>
              <td class="px-3 py-2">${cov[i].toFixed(1)}%</td>
              <td class="px-3 py-2">${cov[i] >= 60 ? 'Baik' : (cov[i] >= 40 ? 'Sedang' : 'Perlu Perbaikan')}</td>
            </tr>`).join('');
        }
      }

      function buildTrendAndComparison() {
        // trend general
        const el1 = document.getElementById('trendChart');
        if (el1) {
          const labels = TREND.labels || [];
          const values = TREND.values || [];
          new Chart(el1.getContext('2d'), {
            type:'line',
            data:{ labels, datasets:[{ label:'Rata-rata (%)', data:values, tension:.1, fill:true, borderColor:'#ef4444', backgroundColor:'rgba(239,68,68,.1)'}]},
            options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{ y:{ beginAtZero:true, ticks:{ callback:v=>v+'%' } } } }
          });
        }

        // comparison (periode A vs B)
        const el2 = document.getElementById('compareChart');
        if (el2) {
          const labels = ['Stunting','Hotspot','Cakupan'];
          const A = [60, 30, 55]; // dummy
          const B = [55, 24, 58]; // dummy
          new Chart(el2.getContext('2d'), {
            type:'bar',
            data:{ labels, datasets:[
              { label: COMPARE.periodA || 'A', data:A, backgroundColor:'#374151' },
              { label: COMPARE.periodB || 'B', data:B, backgroundColor:'#ef4444' }
            ]},
            options:{ responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true } } }
          });
        }

        // comparison antar desa
        const el3 = document.getElementById('compareByDesaChart');
        if (el3) {
          const labels = (RANKING||[]).slice(0,10).map(r=>r.desa);
          const a = (RANKING||[]).slice(0,10).map(r=>r.rate);
          const b = a.map(v => Math.max(0, v + (Math.random()*4-2))); // dummy perubahan
          new Chart(el3.getContext('2d'), {
            type:'line',
            data:{ labels, datasets:[
              { label:'Periode A', data:a, borderColor:'#374151', backgroundColor:'rgba(17,24,39,.1)', fill:true, tension:.1 },
              { label:'Periode B', data:b, borderColor:'#ef4444', backgroundColor:'rgba(239,68,68,.1)', fill:true, tension:.1 }
            ]},
            options:{ responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true, ticks:{ callback:v=>v+'%' } } } }
          });
        }
      }

      // ======= Thematic Map =======
      function buildMap() {
        const el = document.getElementById('map-thematic');
        if (!el) return;
        const map = L.map('map-thematic').setView([-7.3167,107.5833], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'© OpenStreetMap' }).addTo(map);
        const bounds = [];
        (MAPDATA||[]).forEach(d => {
          if (d.lat==null || d.lng==null) return;
          const color = colorByRate(d.rate || 0);
          const r = d.confidence>=99?600:(d.confidence>=95?400:(d.confidence>=90?200:120));
          const popup = `<div class="p-1"><div class="font-semibold">${d.desa}</div>
                         <div class="text-xs text-gray-700">Rate: ${(d.rate??0).toFixed(1)}% • Conf: ${d.confidence??'-'}%</div></div>`;
          L.circleMarker([d.lat,d.lng],{radius:8,fillColor:color,color:'#fff',weight:2,opacity:1,fillOpacity:.85}).addTo(map).bindPopup(popup);
          L.circle([d.lat,d.lng],{radius:r,fillColor:color,color:color,weight:1,opacity:.3,fillOpacity:.08}).addTo(map);
          bounds.push([d.lat,d.lng]);
        });
        if (bounds.length) map.fitBounds(bounds,{padding:[20,20]});
      }

      // ======= Export Quick Actions =======
      document.getElementById('btnExportPng')?.addEventListener('click', () => {
        const canvases = ['trendChart','rankChart','coverageChart','hotspotTrendChart','compareByDesaChart','compareChart']
          .map(id => document.getElementById(id)).filter(Boolean);
        canvases.forEach((c,idx) => {
          const a = document.createElement('a');
          a.download = `chart_${idx+1}.png`;
          a.href = c.toDataURL('image/png');
          a.click();
        });
      });

      document.getElementById('btnExportCsv')?.addEventListener('click', () => {
        const rows = (RANKING||[]).map(r => [r.desa, r.rate]);
        let csv = 'desa,rate\n' + rows.map(r=>`${r[0]},${r[1]}`).join('\n');
        const blob = new Blob([csv], {type: 'text/csv'});
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'ranking.csv';
        a.click();
      });

      // ======= Boot =======p
      document.addEventListener('DOMContentLoaded', () => {
        switchPeriodInput();
        buildRankChart();
        buildHotspotTrend();
        buildCoverageChart();
        buildTrendAndComparison();
        buildMap();
      });
    </script>
  @endpush
</x-layout>
