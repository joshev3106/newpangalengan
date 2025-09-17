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
            <div class="mb-2 rounded-lg bg-green-50 text-green-700 px-4 py-3">
                {{ session('ok') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-2 rounded-lg bg-red-50 text-red-700 px-4 py-3">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div id="tambahData" class="flex items-center justify-between gap-4 mb-1">
            <div class="flex flex-wrap gap-2">
                @auth
                    <a href="{{ route('stunting.create') }}"
                       class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800">+ Tambah Data</a>
                @endauth
            </div>
        </div>

        {{-- Filter Bar (server-side) --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-4 md:p-5">
          <form method="GET" action="{{ route('stunting.index') }}" id="filterForm" class="flex flex-col md:flex-row gap-3 justify-between">
              <div class="flex flex-col md:flex-row gap-2 justify-between w-full">
                <input type="hidden" name="view" id="viewInput" value="{{ $currentView }}">

                @if ($currentView==='table')
                  <div class="relative w-full">
                      <input name="q" id="q" type="text" placeholder="Cari desa …"
                             value="{{ $q ?? '' }}"
                             class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500 pl-10">
                      <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
                      </svg>
                  </div>
                @endif

                <div class="flex gap-2 flex-col md:flex-row">
                  @if ($currentView==='table')
                    <select name="severity" id="severity"
                            class="rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">
                        <option value="">Semua tingkat</option>
                        <option value="high"   @selected(($sev ?? '') === 'high')>Tinggi (&gt;20%)</option>
                        <option value="medium" @selected(($sev ?? '') === 'medium')>Sedang (10&ndash;20%)</option>
                        <option value="low"    @selected(($sev ?? '') === 'low')>Rendah (&lt;10%)</option>
                    </select>
                  @endif

                  <input name="period" id="period" type="month" value="{{ $period ?? '' }}"
                         class="rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">

                  <div class="flex items-center gap-2 pt-1">
                      <button class="px-4 py-2 rounded-lg hover:cursor-pointer w-full bg-red-600 text-white hover:bg-red-500" type="submit">
                          Terapkan
                      </button>
                      @if(request()->has('period'))
                        <a href="{{ route('stunting.index', ['view' => $currentView]) }}" class="px-4 py-2 w-full text-center rounded-lg bg-gray-100 hover:bg-gray-200">
                            Reset
                        </a>
                      @endif
                  </div>
                </div>
              </div>
          </form>
        </div>

        {{-- Mini navbar (Tabs) --}}
        @php $q = request()->query(); @endphp
        <div class="mt-3 mb-6 w-full">
          <div class="inline-flex rounded-xl bg-gray-100 p-1 w-full items-center">
            <a
              href="{{ route('stunting.index', array_merge($q, ['view' => 'table'])) }}"
              class="px-4 py-2 w-full text-center rounded-lg text-sm font-medium {{ ($currentView==='table') ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
              Tabel
            </a>
            <a
              href="{{ route('stunting.index', array_merge($q, ['view' => 'chart'])) }}"
              class="px-4 py-2 w-full text-center rounded-lg text-sm font-medium {{ ($currentView==='chart') ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
              Chart
            </a>
          </div>
        </div>

        @if (!empty($periodLabel))
          <div class="mb-3 text-sm text-gray-600">
            Menampilkan data periode: <span class="font-semibold">{{ $periodLabel }}</span>
          </div>
        @else
          <div class="mb-3 text-sm text-gray-600">
            Menampilkan <span class="font-semibold">data terbaru</span>:
            <span class="font-semibold">{{ $displayPeriodLabel ?? '-' }}</span>.
          </div>
        @endif

        {{-- ===== Tab: CHART ===== --}}
        <section id="tab-chart" class="{{ $currentView==='chart' ? '' : 'hidden' }}">
          <div class="bg-white rounded-2xl w-full mb-2 shadow-sm ring-1 ring-gray-100 p-4 border-b border-gray-200 flex justify-between">
            <div>
              <h1 class="text-lg font-semibold text-gray-800">Chart Data Stunting</h1>
            </div>
            <div id="modalKeterangan">
              <button id="openModalBtnChart" class="flex items-center p-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 hover:cursor-pointer focus:ring-offset-2 transition-colors duration-200">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
              </button>
              <div id="modalOverlayChart" class="fixed inset-0 bg-opacity-50 z-50 hidden">
                  <div class="flex items-center justify-center min-h-screen p-4">
                      <div class="bg-white rounded-lg shadow-xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" id="modalContentChart">
                          <div class="flex items-center justify-between p-6 border-b border-gray-200">
                              <h3 class="text-lg font-semibold text-gray-900">
                                  Keterangan Data
                              </h3>
                              <button id="closeModalBtnChart" class="text-gray-400 hover:cursor-pointer hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors duration-200">
                                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                  </svg>
                              </button>
                          </div>
                          <div class="flex flex-col">
                            <div class="flex items-center gap-2 p-2 border-b border-gray-100">
                              <h1 class="font-semibold uppercase text-sm">Ranking Desa</h1>
                              <p class="text-sm">: Desa dengan tingkat (%) tertinggi pada periode terpilih.</p>
                            </div>
                            <div class="flex items-center gap-2 p-2 border-b border-gray-100">
                              <h1 class="font-semibold uppercase text-sm">Rata-rata Bulanan</h1>
                              <p class="text-sm">: (Σ kasus ÷ Σ populasi × 100) agregat seluruh desa per bulan (12 bulan terakhir).</p>
                            </div>
                          </div>
                          <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                              <div class="flex justify-end">
                                  <button id="closeModalFooterBtnChart" class="hover:cursor-pointer px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                                      Tutup
                                  </button>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
          </div>
          <div class="flex flex-col md:flex-row gap-2">
            <div class="bg-white rounded-2xl w-full shadow-sm ring-1 ring-gray-100 p-4">
              <h3 class="font-semibold mb-3">Ranking Desa (%)</h3>
              <div class="h-80 md:h-96"><canvas id="rankingChart"></canvas></div>
            </div>
            <div class="bg-white rounded-2xl w-full shadow-sm ring-1 ring-gray-100 p-4">
              <h3 class="font-semibold mb-3">Rata-rata Bulanan Dalam Satu Tahun Kebelakang</h3>
              <div class="h-80 md:h-96"><canvas id="trendChart"></canvas></div>
            </div>
          </div>
        </section>

        @php
          $qsAll = request()->query();
          $mkSortUrl = function(string $col) use ($qsAll, $sort, $dir) {
              $nextDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
              return route('stunting.index', array_merge($qsAll, [
                  'sort' => $col,
                  'dir'  => $nextDir,
                  'view' => 'table',
              ]));
          };
          $sortArrow = function(string $col) use ($sort, $dir) {
              if ($sort !== $col) return '';
              return $dir === 'asc' ? '↑' : '↓';
          };
        @endphp

        {{-- ===== Tab: TABLE ===== --}}
        <section id="tab-table" class="{{ $currentView==='table' ? '' : 'hidden' }}">
          <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100">
              <div class="bg-white rounded-t-2xl w-full shadow-sm ring-1 ring-gray-100 p-4 border-b border-gray-200 flex justify-between">
                <div>
                  <h1 class="text-lg font-semibold text-gray-800">Table Data Stunting</h1>
                </div>
                <div id="modalKeterangan">
                  <button id="openModalBtnTable" class="flex items-center p-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 hover:cursor-pointer focus:ring-offset-2 transition-colors duration-200">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                  </button>

                  <div id="modalOverlayTable" class="fixed inset-0 bg-opacity-50 z-50 hidden">
                      <div class="flex items-center justify-center min-h-screen p-4">
                          <div class="bg-white rounded-lg shadow-xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" id="modalContentTable">
                              <div class="flex items-center justify-between p-6 border-b border-gray-200">
                                  <h3 class="text-lg font-semibold text-gray-900">
                                      Keterangan Data
                                  </h3>
                                  <button id="closeModalBtnTable" class="text-gray-400 hover:cursor-pointer hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors duration-200">
                                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                      </svg>
                                  </button>
                              </div>

                              <div class="flex flex-col">
                                <div class="flex items-center  gap-2 p-2 border-b border-gray-100">
                                  <h1 class="font-semibold uppercase text-sm">Kasus</h1>
                                  <p class="text-sm">: Jumlah stunting tercatat.</p>
                                </div>
                                <div class="flex items-center  gap-2 p-2 border-b border-gray-100">
                                  <h1 class="font-semibold uppercase text-sm">Populasi</h1>
                                  <p class="text-sm">: Total penduduk per desa.</p>
                                </div>
                                <div class="flex items-center  gap-2 p-2 border-b border-gray-100">
                                  <h1 class="font-semibold uppercase text-sm">Tingkat</h1>
                                  <p class="text-sm">: Persentase kasus terhadap populasi.</p>
                                </div>
                                <div class="flex items-center  gap-2 p-2 border-b border-gray-100">
                                  <h1 class="font-semibold uppercase text-sm">Periode</h1>
                                  <p class="text-sm">: Rentang waktu data (Bulanan).</p>
                                </div>
                              </div>

                              <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                                  <div class="flex justify-end">
                                      <button id="closeModalFooterBtnTable" class="px-4 py-2 hover:cursor-pointer bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                                          Tutup
                                      </button>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                </div>
              </div>

              <div class="table-scroll">
                  <table class="min-w-full text-left text-sm">
                    <thead>
                      <tr class="text-gray-600 border-b">
                        <th class="sticky px-4 py-3 font-semibold uppercase tracking-wider">
                          <a href="{{ $mkSortUrl('desa') }}" class="inline-flex items-center gap-1 hover:underline">
                            Desa <span>{{ $sortArrow('desa') }}</span>
                          </a>
                        </th>
                        <th class="sticky px-4 py-3 font-semibold uppercase tracking-wider">
                          <a href="{{ $mkSortUrl('kasus') }}" class="inline-flex items-center gap-1 hover:underline">
                            Kasus <span>{{ $sortArrow('kasus') }}</span>
                          </a>
                        </th>
                        <th class="sticky px-4 py-3 font-semibold uppercase tracking-wider">
                          <a href="{{ $mkSortUrl('populasi') }}" class="inline-flex items-center gap-1 hover:underline">
                            Populasi <span>{{ $sortArrow('populasi') }}</span>
                          </a>
                        </th>
                        <th class="sticky px-4 py-3 font-semibold uppercase tracking-wider">
                          <a href="{{ $mkSortUrl('rate') }}" class="inline-flex items-center gap-1 hover:underline">
                            Tingkat <span class="text-xs text-gray-500">(%)</span> <span>{{ $sortArrow('rate') }}</span>
                          </a>
                        </th>
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
                                $periodText = \Illuminate\Support\Carbon::parse($d->period)->isoFormat("MMM 'YY");
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
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
  <script>
    if (window.ChartDataLabels && !Chart.registry.plugins.get('datalabels')) {
      Chart.register(window.ChartDataLabels);
    }

    let rankingChartInstance = null;
    let trendChartInstance   = null;

    const colorByRate = (v) => v > 20 ? '#dc2626' : (v >= 10 ? '#f97316' : '#16a34a');
    const pad5 = (v) => (v <= 0 ? 5 : Math.ceil((v * 1.15) / 5) * 5);

    async function loadChartsIfNeeded() {
      if (rankingChartInstance && trendChartInstance) return;

      const periodParam = @json($period ?? null);
      const url = new URL(@json(route('stunting.chart'))); // -> /stunting/chart-data
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

      // ===== Ranking (Top 25) =====
      const ranking = Array.isArray(json.ranking) ? json.ranking : [];
      const rWrap   = document.getElementById('rankingChart')?.parentElement;

      if (!ranking.length) {
        if (rWrap) rWrap.innerHTML = '<div class="h-80 md:h-96 grid place-items-center text-sm text-gray-500">Tidak ada data ranking untuk periode ini.</div>';
      } else {
        const labels = ranking.map(r => r.desa);
        const data   = ranking.map(r => r.rate);
        const colors = data.map(colorByRate);
        const maxRate = Math.max(...data);

        const rCanvas = document.getElementById('rankingChart');
        const rctx = rCanvas.getContext('2d');
        rankingChartInstance = new Chart(rctx, {
          type: 'bar',
          data: {
            labels,
            datasets: [{ label: 'Tingkat (%) — Top 25', data, backgroundColor: colors, borderWidth: 0 }]
          },
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

      // ===== Trend =====
      const tLabels = (json.periods ?? []);
      const tData   = (json.trend   ?? []);
      const maxTrend = tData.length ? Math.max(...tData) : 0;

      const tCanvas = document.getElementById('trendChart');
      if (tCanvas) {
        const tctx = tCanvas.getContext('2d');
        trendChartInstance = new Chart(tctx, {
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

    const viewInput = document.getElementById('viewInput');
    const tabTable  = document.getElementById('tab-table');
    const tabChart  = document.getElementById('tab-chart');

    function setActiveTab(name) {
      if (!tabTable || !tabChart) return;
      if (name === 'chart') {
        tabChart.classList.remove('hidden');
        tabTable.classList.add('hidden');
        loadChartsIfNeeded();
      } else {
        tabTable.classList.remove('hidden');
        tabChart.classList.add('hidden');
      }
      if (viewInput) viewInput.value = name;
      const url = new URL(window.location.href);
      url.searchParams.set('view', name);
      window.history.replaceState({}, '', url.toString());
    }

    document.addEventListener('DOMContentLoaded', () => {
      const initial = @json($currentView ?? 'table');
      setActiveTab(initial);
    });

    const f = document.getElementById('filterForm');
    const sev = document.getElementById('severity');
    const period = document.getElementById('period');
    if (f && sev)    sev.addEventListener('change',   () => f.submit());
    if (f && period) period.addEventListener('change',() => f.submit());
    const qInp = document.getElementById('q');
    if (f && qInp) qInp.addEventListener('keydown', (e) => { if (e.key === 'Enter') f.submit(); });
  </script>

  {{-- Modal scripts untuk Table & Chart (tetap) --}}
  <script>
      // TABLE modal
      const openModalBtnTable = document.getElementById('openModalBtnTable');
      const modalOverlayTable = document.getElementById('modalOverlayTable');
      const modalContentTable = document.getElementById('modalContentTable');
      const closeModalBtnTable = document.getElementById('closeModalBtnTable');
      const closeModalFooterBtnTable = document.getElementById('closeModalFooterBtnTable');
      function openModal() {
          modalOverlayTable.classList.remove('hidden');
          setTimeout(() => {
              modalContentTable.classList.remove('scale-95', 'opacity-0');
              modalContentTable.classList.add('scale-100', 'opacity-100');
          }, 10);
      }
      function closeModal() {
          modalContentTable.classList.remove('scale-100', 'opacity-100');
          modalContentTable.classList.add('scale-95', 'opacity-0');
          setTimeout(() => { modalOverlayTable.classList.add('hidden'); }, 300);
      }
      openModalBtnTable?.addEventListener('click', openModal);
      closeModalBtnTable?.addEventListener('click', closeModal);
      closeModalFooterBtnTable?.addEventListener('click', closeModal);
      modalOverlayTable?.addEventListener('click', function(e) { if (e.target === modalOverlayTable) closeModal(); });
      document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && !modalOverlayTable.classList.contains('hidden')) closeModal(); });
  </script>

  <script>
      // CHART modal
      const openModalBtnChart = document.getElementById('openModalBtnChart');
      const modalOverlayChart = document.getElementById('modalOverlayChart');
      const modalContentChart = document.getElementById('modalContentChart');
      const closeModalBtnChart = document.getElementById('closeModalBtnChart');
      const closeModalFooterBtnChart = document.getElementById('closeModalFooterBtnChart');
      function openModal() {
          modalOverlayChart.classList.remove('hidden');
          setTimeout(() => {
              modalContentChart.classList.remove('scale-95', 'opacity-0');
              modalContentChart.classList.add('scale-100', 'opacity-100');
          }, 10);
      }
      function closeModal() {
          modalContentChart.classList.remove('scale-100', 'opacity-100');
          modalContentChart.classList.add('scale-95', 'opacity-0');
          setTimeout(() => { modalOverlayChart.classList.add('hidden'); }, 300);
      }
      openModalBtnChart?.addEventListener('click', openModal);
      closeModalBtnChart?.addEventListener('click', closeModal);
      closeModalFooterBtnChart?.addEventListener('click', closeModal);
      modalOverlayChart?.addEventListener('click', function(e) { if (e.target === modalOverlayChart) closeModal(); });
      document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && !modalOverlayChart.classList.contains('hidden')) closeModal(); });
  </script>
@endpush

</x-layout>
