<x-layout>
    @push('styles')
        <style>
            .table-scroll { overflow: auto; }
            th.sticky { position: sticky; top: 0; background: #fff; z-index: 5; }
        </style>
    @endpush

    @php
      $currentView = request('view', 'table'); // 'table' | 'chart'
    @endphp

    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- Flash message --}}
        @if (session('ok'))
            <div class="mb-4 rounded-lg bg-green-50 text-green-700 px-4 py-3">
                {{ session('ok') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 text-red-700 px-4 py-3">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center justify-between gap-4 mb-6">
            <div class="flex flex-wrap gap-2">
                @auth
                    <a href="{{ route('stunting.create') }}"
                       class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800">+ Tambah Data</a>
                @endauth
            </div>
        </div>

        {{-- Filter Bar (server-side) --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-4 md:p-5">
          <form method="GET" action="{{ route('stunting.index') }}" id="filterForm" class="grid md:grid-cols-4 gap-3">
              {{-- simpan view aktif agar konsisten setelah submit --}}
              <input type="hidden" name="view" id="viewInput" value="{{ $currentView }}">

              <div class="relative">
                  <input name="q" id="q" type="text" placeholder="Cari desa …"
                         value="{{ $q ?? '' }}"
                         class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500 pl-10">
                  <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
                  </svg>
              </div>

              <select name="severity" id="severity"
                      class="rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">
                  <option value="">Semua tingkat</option>
                  <option value="high"   @selected(($sev ?? '') === 'high')>Tinggi (&gt;20%)</option>
                  <option value="medium" @selected(($sev ?? '') === 'medium')>Sedang (10&ndash;20%)</option>
                  <option value="low"    @selected(($sev ?? '') === 'low')>Rendah (&lt;10%)</option>
              </select>

              {{-- Satu input month utk pilih periode --}}
              <input name="period" id="period" type="month" value="{{ $period ?? '' }}"
                     class="rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">

              <div class="md:col-span-4 flex items-center gap-2 pt-1">
                  <button class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-500" type="submit">
                      Terapkan
                  </button>
                  <a href="{{ route('stunting.index', ['view' => $currentView]) }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">
                      Reset
                  </a>
              </div>
          </form>
        </div>

        {{-- Mini navbar (Tabs) --}}
        <div class="mt-3 mb-6">
          <div class="inline-flex rounded-xl bg-gray-100 p-1">
            <button type="button"
                    data-tab="table"
                    class="tab-btn px-4 py-2 rounded-lg text-sm font-medium {{ $currentView==='table' ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
              📋 Tabel
            </button>
            <button type="button"
                    data-tab="chart"
                    class="tab-btn px-4 py-2 rounded-lg text-sm font-medium {{ $currentView==='chart' ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
              📈 Grafik
            </button>
          </div>
        </div>

        @if (!empty($period))
          <div class="mb-3 text-sm text-gray-600">
            Menampilkan data periode: <span class="font-semibold">{{ $period }}</span>
          </div>
        @else
          <div class="mb-3 text-sm text-gray-600">
            Menampilkan <span class="font-semibold">data terbaru</span> per desa (periode terakhir yang tersedia).
          </div>
        @endif

        {{-- ===== Tab: CHART ===== --}}
        <section id="tab-chart" class="{{ $currentView==='chart' ? '' : 'hidden' }}">
          <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-4">
              <h3 class="font-semibold mb-3">Ranking Desa (%)</h3>
              <div class="h-80 md:h-96"><canvas id="rankingChart"></canvas></div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-4">
              <h3 class="font-semibold mb-3">Tren Rata-rata Bulanan</h3>
              <div class="h-80 md:h-96"><canvas id="trendChart"></canvas></div>
            </div>
          </div>
        </section>

        {{-- ===== Tab: TABLE ===== --}}
        <section id="tab-table" class="{{ $currentView==='table' ? '' : 'hidden' }}">
          <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100">
              <div class="table-scroll">
                  <table class="min-w-full text-left text-sm">
                      <thead>
                          <tr class="text-gray-600 border-b">
                              <th class="sticky px-4 py-3 font-semibold">Desa</th>
                              <th class="sticky px-4 py-3 font-semibold">Kasus</th>
                              <th class="sticky px-4 py-3 font-semibold">Populasi</th>
                              <th class="sticky px-4 py-3 font-semibold">Tingkat</th>
                              <th class="sticky px-4 py-3 font-semibold">Periode</th>
                              @auth
                                  <th class="sticky px-4 py-3 font-semibold">Aksi</th>
                              @endauth
                          </tr>
                      </thead>
                      <tbody class="divide-y">
                          @forelse ($rows as $d)
                              @php
                                  $rate = isset($d->rate) ? $d->rate : ($d->populasi > 0 ? round(($d->kasus / $d->populasi) * 100, 1) : 0);
                                  $sevRow = isset($d->severity) ? $d->severity : ($rate > 20 ? 'high' : ($rate >= 10 ? 'medium' : 'low'));
                                  $clr = $sevRow === 'high' ? 'bg-red-600 text-white'
                                      : ($sevRow === 'medium' ? 'bg-orange-500 text-white' : 'bg-green-500 text-white');
                                  $periodText = \Illuminate\Support\Carbon::parse($d->period)->format('Y-m');
                              @endphp
                              <tr class="hover:bg-gray-50">
                                  <td class="px-4 py-3 font-medium text-gray-800">{{ $d->desa }}</td>
                                  <td class="px-4 py-3">{{ $d->kasus }}</td>
                                  <td class="px-4 py-3">{{ number_format($d->populasi) }}</td>
                                  <td class="px-4 py-3">
                                      <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $clr }}">
                                          {{ number_format($rate, 1) }}%
                                      </span>
                                  </td>
                                  <td class="px-4 py-3">{{ $periodText }}</td>

                                  @auth
                                  <td class="px-4 py-3 whitespace-nowrap">
                                      <a href="{{ route('stunting.edit', $d) }}" class="text-blue-600 hover:underline">Edit</a>
                                      <form action="{{ route('stunting.destroy', $d) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Hapus data ini?')">
                                          @csrf @method('DELETE')
                                          <button class="text-red-600 hover:underline ml-2 hover:cursor-pointer">Hapus</button>
                                      </form>
                                  </td>
                                  @endauth
                              </tr>
                          @empty
                              <tr>
                                  <td colspan="6" class="px-4 py-6 text-center text-gray-500">Tidak ada data</td>
                              </tr>
                          @endforelse
                      </tbody>
                  </table>
              </div>

              {{-- Footer / pagination --}}
              <div class="px-4 py-3 border-t flex items-center justify-between text-sm text-gray-600">
                  <div>
                      @if ($rows->total() > 0)
                          Menampilkan {{ $rows->firstItem() }}–{{ $rows->lastItem() }} dari {{ $rows->total() }} data
                      @else
                          0 data
                      @endif
                  </div>
                  <div>
                    {{ $rows->onEachSide(1)->links('pagination.red') }}
                  </div>
              </div>
          </div>
        </section>
    </div>

    @push('scripts')
        {{-- Chart libs untuk tab Grafik --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

        <script>
          // Register datalabels sekali
          if (window.ChartDataLabels && !Chart.registry.plugins.get('datalabels')) {
            Chart.register(window.ChartDataLabels);
          }

          // Simpan instance supaya tidak dobel render
          let rankingChartInstance = null;
          let trendChartInstance   = null;
          const colorByRate = (v) => v > 20 ? '#dc2626' : (v >= 10 ? '#f97316' : '#16a34a');

          async function loadChartsIfNeeded() {
            // kalau sudah ada instance, cukup return
            if (rankingChartInstance && trendChartInstance) return;

            const periodParam = @json($period ?? null);
            const url = new URL(@json(route('stunting.chart')), window.location.origin);
            if (periodParam) url.searchParams.set('period', periodParam);

            const res  = await fetch(url);
            const json = await res.json();

            // ------- RANKING (horizontal bar) -------
            const labels = json.ranking.map(r => r.desa);
            const data   = json.ranking.map(r => r.rate);
            const colors = data.map(colorByRate);

            const rctx = document.getElementById('rankingChart').getContext('2d');
            rankingChartInstance = new Chart(rctx, {
              type: 'bar',
              data: {
                labels,
                datasets: [{ label: 'Tingkat (%)', data, backgroundColor: colors, borderWidth: 0 }]
              },
              options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                  x: { beginAtZero: true, suggestedMax: 35, ticks: { callback: v => v + '%' } },
                  y: { ticks: { autoSkip: false } }
                },
                plugins: {
                  legend: { display: false },
                  tooltip: { callbacks: { label: ctx => `${ctx.raw}%` } },
                  datalabels: { anchor: 'end', align: 'right', formatter: v => v + '%', clamp: true }
                }
              }
            });

            // ------- TREND (line) -------
            const tctx = document.getElementById('trendChart').getContext('2d');
            trendChartInstance = new Chart(tctx, {
              type: 'line',
              data: {
                labels: json.periods,
                datasets: [{ label: 'Rata-rata (%)', data: json.trend, fill: false, pointRadius: 3, tension: .25 }]
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { callback: v => v + '%' } } },
                plugins: {
                  legend: { display: false },
                  datalabels: { align: 'top', anchor: 'end', formatter: v => v + '%' }
                }
              }
            });
          }

          // Tabs logic
          const viewInput = document.getElementById('viewInput');
          const tabBtns   = document.querySelectorAll('.tab-btn');
          const tabTable  = document.getElementById('tab-table');
          const tabChart  = document.getElementById('tab-chart');

          function setActiveTab(name) {
            // toggle content
            if (name === 'chart') {
              tabChart.classList.remove('hidden');
              tabTable.classList.add('hidden');
              loadChartsIfNeeded(); // render chart jika belum
            } else {
              tabTable.classList.remove('hidden');
              tabChart.classList.add('hidden');
            }
            // update tombol
            tabBtns.forEach(btn => {
              const active = btn.dataset.tab === name;
              btn.classList.toggle('bg-white', active);
              btn.classList.toggle('shadow', active);
              btn.classList.toggle('text-gray-900', active);
              btn.classList.toggle('text-gray-600', !active);
            });
            // simpan ke input hidden & URL (tanpa reload)
            if (viewInput) viewInput.value = name;
            const url = new URL(window.location.href);
            url.searchParams.set('view', name);
            window.history.replaceState({}, '', url.toString());
          }

          tabBtns.forEach(btn => {
            btn.addEventListener('click', () => setActiveTab(btn.dataset.tab));
          });

          // Inisialisasi sesuai server state
          document.addEventListener('DOMContentLoaded', () => {
            const initial = @json($currentView);
            setActiveTab(initial);
          });

          // Submit otomatis saat filter diubah (opsional)
          const f = document.getElementById('filterForm');
          const sev = document.getElementById('severity');
          const period = document.getElementById('period');
          if (sev)    sev.addEventListener('change',   () => f.submit());
          if (period) period.addEventListener('change',() => f.submit());

          const q = document.getElementById('q');
          if (q) q.addEventListener('keydown', (e) => {
              if (e.key === 'Enter') f.submit();
          });
        </script>
    @endpush
</x-layout>
