{{-- resources/views/home/index.blade.php --}}
<x-layout>
  @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
    <style>
      .table-scroll { overflow: auto; }
      th.sticky { position: sticky; top: 0; background: #fff; z-index: 5; }
      #mini-map { height: 360px; width: 100%; }
    </style>
  @endpush

  @php
    // ====== Fallback & formatting ======
    $periodParam           = request('period'); // 'YYYY-MM' (opsional filter)
    $periodLabel           = $displayPeriodLabel ?? ($lastUpdateLabel ?? '-');
    $lastUpdateLabel       = $lastUpdateLabel ?? $displayPeriodLabel ?? '-';
    $avgRateHome           = isset($avgRateHome) ? (float) $avgRateHome : null;

    // Hotspot stats: {high, medium, low, not, total}
    $hs = array_merge(['high'=>0,'medium'=>0,'low'=>0,'not'=>0,'total'=>0], (array) ($hotspotStats ?? []));

    // Top list fallback
    $topStunting  = collect($topStunting ?? [])->take(5)->values();
    $topHotspots  = collect($topHotspots ?? [])->take(5)->values();

    // Puskesmas count fallback (ambil dari config bila controller tidak memberi)
    $puskesmasCount = $puskesmasCount ?? (is_array(config('desa_puskesmas.pk_coords')) ? count(config('desa_puskesmas.pk_coords')) : 0);

    $fmt = fn($n) => number_format((int) ($n ?? 0));
    $fmtPct = fn($v) => is_null($v) ? '—' : number_format((float)$v, 1).'%' ;
    $badgeClr = function($rate) {
        if ($rate > 20) return 'bg-red-600 text-white';
        if ($rate >= 10) return 'bg-orange-500 text-white';
        return 'bg-green-500 text-white';
    };
    $confBadge = function($conf) {
        return $conf===99 ? 'bg-red-100 text-red-800' : ($conf===95 ? 'bg-orange-100 text-orange-800' : ($conf===90 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'));
    };
  @endphp

  <header class="relative overflow-hidden">
    {{-- Background gradient + decorative glow --}}
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-red-700/90 via-red-600 to-red-700/90"></div>

    {{-- Top strip --}}
    <div class="bg-black/10 backdrop-blur-sm">
      <div class="max-w-7xl mx-auto px-4 py-2">
        <p class="text-center text-red-100/90 text-sm hidden md:block">
          Sistem Informasi Data Stunting Kecamatan Pangalengan
        </p>
        <div class="text-center text-red-100/90 text-sm md:hidden leading-tight">
          <p>Sistem Informasi Data Stunting</p>
          <p>Kecamatan Pangalengan</p>
        </div>
      </div>
    </div>

    {{-- Hero --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-7">
      <div class="flex flex-col lg:flex-row items-center justify-between gap-6">

        {{-- Logo + Title --}}
        <div class="flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left">
          <div class="rounded-full p-2.5 bg-white/15 ring-1 ring-white/20 backdrop-blur">
            <img src="{{ asset('img/logo-kab-bandung.png') }}" alt="Logo Kabupaten Bandung" class="h-14 w-14 object-contain">
          </div>

          <div class="text-white">
            <div class="rounded-xl px-4 py-3 bg-white/5 ring-1 ring-white/15 backdrop-blur">
              <p class="text-xl sm:text-2xl font-semibold tracking-tight">Kecamatan Pangalengan</p>
              <p class="text-red-100/90 text-xs sm:text-sm">Kabupaten Bandung • Provinsi Jawa Barat</p>

              <div class="mt-2 flex flex-wrap justify-center sm:justify-start items-center gap-2 text-[11px] sm:text-xs text-red-50/90">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/10 ring-1 ring-white/10">
                  {{-- map-pin icon --}}
                  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/></svg>
                  {{ number_format($pkCount) }} Faskes
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/10 ring-1 ring-white/10">
                  {{-- building icon --}}
                  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 21V7a2 2 0 0 1 2-2h3V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1h3a2 2 0 0 1 2 2v14h-3v-3H6v3H3Zm5-7h2v-2H8v2Zm0-4h2V8H8v2Zm4 4h2v-2h-2v2Zm0-4h2V8h-2v2Z"/></svg>
                  {{ number_format($desaMappedCount) }} Desa Terpetakan
                </span>
              </div>
            </div>
          </div>
        </div>

        {{-- KPI ringkas --}}
        <div class="grid grid-cols-3 gap-3 w-full lg:w-auto lg:min-w-[540px]">
          {{-- Total Desa --}}
          <div class="rounded-2xl p-4 bg-white/8 ring-1 ring-white/15 text-red-50 backdrop-blur">
            <div class="flex items-center justify-between">
              <span class="text-[10px] uppercase tracking-wider text-white/80">Total Desa</span>
              <svg class="w-4 h-4 opacity-80" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3l9 7-9 7-9-7 9-7Zm0 10l9 7v1H3v-1l9-7Z"/></svg>
            </div>
            <div class="text-2xl font-bold mt-1">{{ number_format($stats['total']) }}</div>
          </div>

          {{-- Rata-rata (%) --}}
          <div class="rounded-2xl p-4 bg-white/8 ring-1 ring-white/15 text-red-50 backdrop-blur">
            <div class="flex items-center justify-between">
              <span class="text-[10px] uppercase tracking-wider text-white/80">Rata-rata (%)</span>
              <svg class="w-4 h-4 opacity-80" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17h2l3-6 4 8 3-5 2 3h4v2H3v-2Z"/></svg>
            </div>
            <div class="text-2xl font-bold mt-1">{{ number_format($stats['avg'], 1) }}%</div>
          </div>

          {{-- Faskes --}}
          <div class="rounded-2xl p-4 bg-white/8 ring-1 ring-white/15 text-red-50 backdrop-blur">
            <div class="flex items-center justify-between">
              <span class="text-[10px] uppercase tracking-wider text-white/80">Faskes</span>
              <svg class="w-4 h-4 opacity-80" viewBox="0 0 24 24" fill="currentColor"><path d="M10 2h4v4h4v4h-4v4h-4v-4H6V6h4V2Z"/></svg>
            </div>
            <div class="text-2xl font-bold mt-1">{{ number_format($pkCount) }}</div>
          </div>
        </div>
      </div>

      {{-- Distribusi + Filter --}}
      @php
        $t    = max(1, (int)($stats['total'] ?? 0));
        $pHigh= round(($stats['high'] ?? 0) / $t * 100);
        $pMed = round(($stats['medium'] ?? 0) / $t * 100);
        $pLow = round(($stats['low'] ?? 0) / $t * 100);
        $pNot = max(0, 100 - $pHigh - $pMed - $pLow);
      @endphp

      <div class="mt-4 flex flex-col-reverse lg:flex-row gap-5 lg:items-center lg:justify-between">

        {{-- Progress --}}
        <div class="flex-1 lg:max-w-2xl">
          <div class="text-[11px] text-red-50/90 mb-2">Distribusi Tingkat</div>
          <div class="w-full h-3.5 rounded-full overflow-hidden ring-1 ring-white/25 bg-white/10">
            <div class="h-full float-left bg-red-500"    style="width: {{ $pHigh }}%"></div>
            <div class="h-full float-left bg-orange-500" style="width: {{ $pMed }}%"></div>
            <div class="h-full float-left bg-green-500"  style="width: {{ $pLow }}%"></div>
            <div class="h-full float-left bg-gray-300"   style="width: {{ $pNot }}%"></div>
          </div>
          <div class="mt-2 flex flex-wrap gap-2 sm:gap-3 text-[11px] text-red-50/90">
            <span class="inline-flex items-center gap-1"><i class="w-2.5 h-2.5 bg-red-500 inline-block rounded"></i> Tinggi {{ $pHigh }}%</span>
            <span class="inline-flex items-center gap-1"><i class="w-2.5 h-2.5 bg-orange-500 inline-block rounded"></i> Sedang {{ $pMed }}%</span>
            <span class="inline-flex items-center gap-1"><i class="w-2.5 h-2.5 bg-green-500 inline-block rounded"></i> Rendah {{ $pLow }}%</span>
            <span class="inline-flex items-center gap-1"><i class="w-2.5 h-2.5 bg-gray-300 inline-block rounded"></i> Not Sig. {{ $pNot }}%</span>
          </div>
        </div>

        {{-- Filter Periode --}}
        <div class="w-full lg:w-auto rounded-xl px-3 py-3 bg-white/10 ring-1 ring-white/20 backdrop-blur">
          <form method="GET" action="{{ route('home') }}"
                class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-center">
            <input type="month" name="period" value="{{ $period ?? '' }}"
                  class="rounded-lg px-3 py-2 bg-white/95 text-gray-800 text-sm w-full focus:outline-none focus:ring-2 focus:ring-red-400">
  
            <div class="flex gap-2">
              <button class="px-4 py-2 hover:cursor-pointer rounded-lg bg-white text-red-700 font-semibold hover:bg-red-50 text-sm w-full sm:w-auto">
                Terapkan
              </button>
              @if(request()->has('period'))
                <a href="{{ route('home') }}"
                  class="px-3 py-2 rounded-lg bg-white text-gray-600 hover:text-gray-600 font-semibold text-sm text-center w-full sm:w-auto">Reset</a>
              @endif
            </div>
          </form>
          <div class="md:text-right">
            <p class="text-xs text-white/80 mt-1">
              Menampilkan <span class="font-semibold">{{ $periodParam ? 'periode terpilih' : 'data terbaru' }}</span>:
              <span class="font-semibold">{{ $periodLabel }}</span>
            </p>
          </div>
        </div>
      </div>
    </div>
  </header>

  <x-navbar></x-navbar>


  <div class="max-w-7xl mx-auto px-6 py-8">

    {{-- ====== Kartu Ringkas (Stunting / Hotspot / Wilayah / Peta) ====== --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
  {{-- Rata-rata Stunting --}}
  <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100 border-t-4 border-red-600 flex flex-col">
    <div class="text-sm text-gray-500">Rata-rata Stunting</div>
    <div class="text-2xl md:text-3xl font-bold">
      {{ $fmtPct($avgRateHome) }}
    </div>
    <a href="{{ route('stunting.index', array_filter(['period'=>$periodParam])) }}"
       class="inline-block mt-auto pt-3 text-sm text-red-600 hover:underline">Lihat Stunting →</a>
  </div>

  {{-- Hotspot --}}
  <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100 border-t-4 border-orange-500 flex flex-col">
    <div class="text-sm text-gray-500">Hotspot ({{ $fmt($hs['total']) }})</div>
    <div class="mt-1 text-sm text-gray-700 space-y-1 flex-1">
      <div class="flex items-center justify-between">
        <span class="inline-flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>99%</span>
        <span class="font-semibold">{{ $fmt($hs['high']) }}</span>
      </div>
      <div class="flex items-center justify-between">
        <span class="inline-flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-orange-600"></span>95%</span>
        <span class="font-semibold">{{ $fmt($hs['medium']) }}</span>
      </div>
      <div class="flex items-center justify-between">
        <span class="inline-flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>90%</span>
        <span class="font-semibold">{{ $fmt($hs['low']) }}</span>
      </div>
    </div>
    <a href="{{ route('hotspot.index', array_filter(['view'=>'table','period'=>$periodParam])) }}"
       class="inline-block mt-auto pt-3 text-sm text-red-600 hover:underline">Lihat Hotspot →</a>
  </div>

  {{-- Wilayah --}}
  <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100 border-t-4 border-yellow-500 flex flex-col">
    <div class="text-sm text-gray-500">Ringkas Wilayah</div>
    <div class="text-sm text-gray-700 space-y-1 mt-1 flex-1">
      <div class="flex items-center justify-between">
        <span>Desa Terdata</span>
        <span class="font-semibold">{{ $fmt($wilayahDesaCount ?? 0) }}</span>
      </div>
      <div class="flex items-center justify-between">
        <span>Rata-rata Cakupan</span>
        <span class="font-semibold">{{ $wilayahAvgCov !== null ? number_format($wilayahAvgCov,1).'%' : '—' }}</span>
      </div>
    </div>
    <a href="{{ route('wilayah.index') }}"
       class="inline-block mt-auto pt-3 text-sm text-red-600 hover:underline">Lihat Wilayah →</a>
  </div>

  {{-- Peta --}}
  <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100 border-t-4 border-gray-400 flex flex-col">
    <div class="text-sm text-gray-500">Fasilitas Kesehatan</div>
    <div class="text-2xl md:text-3xl font-bold flex-1">{{ $fmt($puskesmasCount) }}</div>
    <a href="{{ route('peta.index', array_filter(['period'=>$periodParam])) }}"
       class="inline-block mt-auto pt-3 text-sm text-red-600 hover:underline">Buka Peta →</a>
  </div>
</div>


    {{-- ====== Snapshot: Stunting & Hotspot ====== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
      {{-- Snapshot Stunting (Top 5 rate) --}}
      <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-4">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-lg font-semibold text-gray-800">Top 5 Desa — Stunting (%)</h3>
          <a href="{{ route('stunting.index', array_filter(['view'=>'table','period'=>$periodParam,'sort'=>'rate','dir'=>'desc'])) }}"
             class="text-sm text-red-600 hover:underline">Selengkapnya</a>
        </div>

        <div class="table-scroll">
          <table class="min-w-full text-left text-sm">
            <thead class="text-gray-600 border-b">
              <tr>
                <th class="sticky px-4 py-2 font-semibold uppercase tracking-wider">Desa</th>
                <th class="sticky px-4 py-2 font-semibold uppercase tracking-wider">Kasus</th>
                <th class="sticky px-4 py-2 font-semibold uppercase tracking-wider">Populasi</th>
                <th class="sticky px-4 py-2 font-semibold uppercase tracking-wider">Tingkat</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              @forelse($topStunting as $r)
                @php
                  $rate = isset($r['rate']) ? $r['rate'] : ($r['populasi'] > 0 ? round(($r['kasus']/$r['populasi'])*100,1) : 0);
                  $clr  = $badgeClr($rate);
                @endphp
                <tr class="hover:bg-gray-50">
                  <td class="px-4 py-2 font-medium text-gray-800">{{ $r['desa'] ?? '-' }}</td>
                  <td class="px-4 py-2">{{ $fmt($r['kasus'] ?? 0) }}</td>
                  <td class="px-4 py-2">{{ $fmt($r['populasi'] ?? 0) }}</td>
                  <td class="px-4 py-2">
                    <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $clr }}">
                      {{ number_format($rate,1) }}%
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="px-4 py-6 text-center text-gray-500">Tidak ada data</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- Snapshot Hotspot (Top 5 confidence & rate) --}}
      <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-4">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-lg font-semibold text-gray-800">Top 5 Hotspot</h3>
          <a href="{{ route('hotspot.index', array_filter(['view'=>'table','period'=>$periodParam,'sort'=>'confidence','dir'=>'desc'])) }}"
             class="text-sm text-red-600 hover:underline">Selengkapnya</a>
        </div>

        <div class="table-scroll">
          <table class="min-w-full text-left text-sm">
            <thead class="text-gray-600 border-b">
              <tr>
                <th class="sticky px-4 py-2 font-semibold uppercase tracking-wider">Desa</th>
                <th class="sticky px-4 py-2 font-semibold uppercase tracking-wider">Kasus</th>
                <th class="sticky px-4 py-2 font-semibold uppercase tracking-wider">Tingkat</th>
                <th class="sticky px-4 py-2 font-semibold uppercase tracking-wider">Confidence</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              @forelse($topHotspots as $h)
                @php
                  $rate = (float) ($h['rate'] ?? 0);
                  $clr  = $badgeClr($rate);
                  $badge= $confBadge($h['confidence'] ?? 0);
                  $label= ($h['confidence'] ?? 0) > 0 ? ($h['confidence'].'%') : 'Not Sig.';
                @endphp
                <tr class="hover:bg-gray-50">
                  <td class="px-4 py-2 font-medium text-gray-800">{{ $h['desa'] ?? $h['name'] ?? '-' }}</td>
                  <td class="px-4 py-2">{{ $fmt($h['cases'] ?? 0) }}</td>
                  <td class="px-4 py-2">
                    <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $clr }}">{{ number_format($rate,1) }}%</span>
                  </td>
                  <td class="px-4 py-2">
                    <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $badge }}">{{ $label }}</span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="px-4 py-6 text-center text-gray-500">Tidak ada data</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- ====== Chart & Peta Mini ====== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      {{-- Chart: Ranking & Trend (pakai endpoint /stunting/chart-data) --}}
      <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-4">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-lg font-semibold text-gray-800">Grafik Ringkas Stunting</h3>
          <div class="text-xs text-gray-500">Periode: <span class="font-medium">{{ $periodParam ?? $periodLabel }}</span></div>
        </div>
        <div class="grid grid-cols-1 gap-4">
          <div class="h-64 md:h-72"><canvas id="homeRankingChart"></canvas></div>
        </div>
      </div>

      {{-- Mini Map Puskesmas --}}
      <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-4">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-lg font-semibold text-gray-800">Peta Faskes (Ringkas)</h3>
          <a href="{{ route('peta.index', array_filter(['period'=>$periodParam])) }}"
             class="text-sm text-red-600 hover:underline">Buka Halaman Peta →</a>
        </div>
        <div id="mini-map" class="rounded-xl overflow-hidden ring-1 ring-gray-100"></div>
      </div>
    </div>

  </div>

  @push('scripts')
    {{-- Chart.js + Datalabels --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

    {{-- Leaflet --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>

    <script>
      // ====== Charts (Ranking & Trend) ======
      if (window.ChartDataLabels && !Chart.registry.plugins.get('datalabels')) {
        Chart.register(window.ChartDataLabels);
      }

      const colorByRate = (v) => v > 20 ? '#dc2626' : (v >= 10 ? '#f97316' : '#16a34a');
      const pad5 = (v) => (v <= 0 ? 5 : Math.ceil((v * 1.15) / 5) * 5);

      async function loadHomeCharts() {
        const url = new URL(@json(route('stunting.chart')));
        const periodParam = @json($periodParam);
        if (periodParam) url.searchParams.set('period', periodParam);

        let json;
        try {
          const res = await fetch(url);
          if (!res.ok) throw new Error('Gagal memuat data chart');
          json = await res.json();
        } catch (e) {
          console.error(e);
          return;
        }

        // Ranking top 10 agar ringkas
        const ranking = (json.ranking ?? []).slice(0,10);
        const labels  = ranking.map(r => r.desa);
        const data    = ranking.map(r => r.rate ?? 0);
        const colors  = data.map(colorByRate);
        const maxRate = data.length ? Math.max(...data) : 0;

        const rc = document.getElementById('homeRankingChart');
        if (rc) {
          new Chart(rc.getContext('2d'), {
            type: 'bar',
            data: { labels, datasets: [{ label: 'Tingkat (%) — Top 10', data, backgroundColor: colors, borderWidth: 0 }]},
            options: {
              indexAxis: 'y',
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                x: { beginAtZero: true, suggestedMax: pad5(maxRate), ticks: { stepSize: 5, callback: v => v + '%' } },
                y: { ticks: { autoSkip: false } }
              },
              plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => `${ctx.raw}%` } },
                datalabels: { anchor: 'end', align: 'right', formatter: (v) => v + '%', clamp: true }
              }
            }
          });
        }

        // Trend 12 bulan
        const tLabels = json.periods ?? [];
        const tData   = json.trend   ?? [];
        const maxTrend= tData.length ? Math.max(...tData) : 0;

        const tc = document.getElementById('homeTrendChart');
        if (tc) {
          new Chart(tc.getContext('2d'), {
            type: 'line',
            data: {
              labels: tLabels,
              datasets: [{
                label: 'Rata-rata (%)',
                data: tData,
                fill: true,
                pointRadius: 3,
                tension: .10,
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderColor: 'rgba(239, 68, 68, 1)'
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                y: { beginAtZero: true, suggestedMax: pad5(maxTrend), ticks: { stepSize: 5, callback: v => v + '%' } }
              },
              plugins: {
                legend: { display: false },
                datalabels: { align: 'top', anchor: 'end', formatter: (v) => v + '%' }
              }
            }
          });
        }
      }

      document.addEventListener('DOMContentLoaded', loadHomeCharts);

      // ====== Mini Map Puskesmas (Ringkas) ======
      document.addEventListener('DOMContentLoaded', () => {
        const pk = @json(config('desa_puskesmas.pk_coords', []));
        const mapEl = document.getElementById('mini-map');
        if (!mapEl) return;

        const map = L.map('mini-map').setView([-7.3167, 107.5833], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'© OpenStreetMap' }).addTo(map);

        const bounds = [];
        Object.keys(pk || {}).forEach(name => {
          const d = pk[name] || {};
          if (d.lat == null || d.lng == null) return;

          const popup = `
            <div class="p-1">
              <div class="font-semibold text-gray-800">${name}</div>
              <div class="text-gray-800 text-xs">Tipe: ${d.tipe ?? '-'}</div>
              <div class="text-gray-800 text-xs">${d.address ?? '-'}</div>
            </div>`;
          L.marker([d.lat, d.lng]).addTo(map).bindPopup(popup);
          bounds.push([d.lat, d.lng]);
        });

        if (bounds.length) map.fitBounds(bounds, { padding:[20,20] });
      });
    </script>
  @endpush
</x-layout>
