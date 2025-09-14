<x-layout>
  @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
    <style>
      #hotspot-map{min-height:400px}
      @media(min-width:768px){#hotspot-map{min-height:600px}}
      th.sticky { position: sticky; top: 0; background: #fff; z-index: 5; }
    </style>
  @endpush

  @php
    $currentView = $currentView ?? request('view','table'); // 'table' | 'map'
    $qsAll = request()->query();

    // helper url sort + toggle asc/desc
    $mkSortUrl = function(string $col) use ($qsAll, $sort, $dir) {
        $nextDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
        return route('hotspot.index', array_merge($qsAll, [
            'sort' => $col,
            'dir'  => $nextDir,
            'view' => 'table', // pastikan tetap di tab Tabel
        ]));
    };
    $sortArrow = function(string $col) use ($sort, $dir) {
        if ($sort !== $col) return '';
        return $dir === 'asc' ? '↑' : '↓';
    };
  @endphp

  <div class="max-w-7xl mx-auto p-6">
    {{-- Flash & errors --}}
    @if(session('ok'))
      <div class="mb-4 rounded-lg bg-green-50 text-green-700 px-4 py-3">{{ session('ok') }}</div>
    @endif
    @if ($errors->any())
      <div class="mb-4 rounded-lg bg-red-50 text-red-700 px-4 py-3">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    {{-- Info periode + Filter --}}
    <div class="flex md:items-center justify-between mb-4 flex-col-reverse w-full md:flex-row">
      @if (!empty($periodLabel))
        <div class="text-sm text-gray-600">
          Menampilkan data periode: <span class="font-semibold">{{ $periodLabel }}</span>
        </div>
      @else
        <div class="text-sm text-gray-600">
          Menampilkan <span class="font-semibold">data terbaru</span>:
          <span class="font-semibold">{{ $displayPeriodLabel ?? '-' }}</span>.
        </div>
      @endif

      <div class="mb-4 bg-transparent rounded-2xl shadow-sm ring-1 ring-gray-100 p-4 md:p-0">
        <form method="GET" id="filterForm" action="{{ route('hotspot.index') }}" class="flex flex-col md:flex-row md:w-md gap-2 items-center">
          <input type="hidden" name="view" value="{{ $currentView }}"> {{-- jaga tab aktif --}}
          <input type="hidden" name="sort" value="{{ $sort ?? 'desa' }}">
          <input type="hidden" name="dir"  value="{{ $dir ?? 'asc' }}">
          <input type="month" name="period" value="{{ request('period') }}"
                 class="rounded-lg w-full border border-gray-300 px-3 py-2">
          <div class="flex gap-2 w-full">
            <button class="px-4 py-2 rounded-lg hover:cursor-pointer w-full bg-red-600 text-white">Terapkan</button>
            @if(request()->has('period'))
              <a href="{{ route('hotspot.index', ['view' => $currentView, 'sort'=>$sort, 'dir'=>$dir]) }}" class="px-3 py-2 w-full text-center rounded-lg bg-gray-100">Reset</a>
            @endif
          </div>
        </form>
      </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-red-600">
        <div class="text-3xl font-bold text-red-600 mb-1" id="high-confidence">{{ $stats['high'] }}</div>
        <div class="text-sm text-gray-600">High <br> Confidence (99%)</div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-orange-600">
        <div class="text-3xl font-bold text-orange-600 mb-1" id="medium-confidence">{{ $stats['medium'] }}</div>
        <div class="text-sm text-gray-600">Medium <br> Confidence (95%)</div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-yellow-500">
        <div class="text-3xl font-bold text-yellow-600 mb-1" id="low-confidence">{{ $stats['low'] }}</div>
        <div class="text-sm text-gray-600">Low <br> Confidence (90%)</div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-gray-400">
        <div class="text-3xl font-bold text-gray-600 mb-1" id="not-significant">{{ $stats['not'] }}</div>
        <div class="text-sm text-gray-600">Not <br> Significant</div>
      </div>
    </div>

    {{-- Mini navbar (Tabs) --}}
    @php $q = request()->query(); @endphp
    <div class="mt-3 mb-6 w-full">
      <div class="inline-flex rounded-xl bg-gray-100 p-1 w-full items-center">
        <a
          href="{{ route('hotspot.index', array_merge($q, ['view' => 'table'])) }}"
          class="px-4 py-2 w-full text-center rounded-lg text-sm font-medium {{ ($currentView==='table') ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
          Tabel
        </a>
        <a
          href="{{ route('hotspot.index', array_merge($q, ['view' => 'map'])) }}"
          class="px-4 py-2 w-full text-center rounded-lg text-sm font-medium {{ ($currentView==='map') ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
          Peta
        </a>
      </div>
    </div>

    {{-- ===== Tab: TABLE ===== --}}
    <section id="tab-table" class="{{ $currentView==='table' ? '' : 'hidden' }}">
      <div class="bg-white rounded-xl shadow-md mb-6">
        <div class="bg-white rounded-t-2xl w-full shadow-sm ring-1 ring-gray-100 p-4 border-b border-gray-200 flex justify-between">
          <div>
            <h1 class="text-lg font-semibold text-gray-800">Table Analisis Hotspot</h1>
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
                            <h1 class="font-semibold uppercase text-sm">Tingkat</h1>
                            <p class="text-sm">: Persentase kasus terhadap populasi.</p>
                          </div>
                          <div class="flex items-center  gap-2 p-2 border-b border-gray-100">
                            <h1 class="font-semibold uppercase text-sm">Confidence</h1>
                            <p class="text-sm">: tingkat keyakinan deteksi hotspot.</p>
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
        <div class="overflow-x-auto">
          <table class="min-w-full text-left text-sm">
            <thead class="bg-gray-50">
              <tr class="text-gray-600 border-b">
                <th class="sticky px-6 py-3 font-semibold uppercase tracking-wider">
                  <a href="{{ $mkSortUrl('desa') }}" class="inline-flex items-center gap-1 hover:underline">
                    Desa <span>{{ $sortArrow('desa') }}</span>
                  </a>
                </th>
                <th class="sticky px-6 py-3 font-semibold uppercase tracking-wider">
                  <a href="{{ $mkSortUrl('cases') }}" class="inline-flex items-center gap-1 hover:underline">
                    Kasus <span>{{ $sortArrow('cases') }}</span>
                  </a>
                </th>
                <th class="sticky px-6 py-3 font-semibold uppercase tracking-wider">
                  <a href="{{ $mkSortUrl('rate') }}" class="inline-flex flex-col items-start gap-1 hover:underline">
                    <div class="flex gap-1">
                      <span>Tingkat</span>
                      <span>{{ $sortArrow('rate') }}</span>
                    </div>
                  </a>
                </th>
                <th class="sticky px-6 py-3 font-semibold uppercase tracking-wider">
                  <a href="{{ $mkSortUrl('confidence') }}" class="inline-flex items-center gap-1 hover:underline">
                    Confidence <span>{{ $sortArrow('confidence') }}</span>
                  </a>
                </th>
              </tr>
            </thead>
            <tbody>
              @foreach ($hotspots as $h)
                @php
                  $conf = $h['confidence'];
                  $cases = $h['cases'];
                  $desa = $h['desa'];
                  $rate = $h['rate'];
                  $badge = $conf===99 ? 'bg-red-100 text-red-800'
                         : ($conf===95 ? 'bg-orange-100 text-orange-800'
                         : ($conf===90 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'));
                  $severity = $rate > 20 ? 'high' : ($rate >= 10 ? 'medium' : 'low'); 
                  $clr = $severity === 'high' ? 'bg-red-600 text-white'
                      : ($severity === 'medium' ? 'bg-orange-500 text-white' : 'bg-green-500 text-white');
                  $label = $conf===0 ? 'Not Significant' : $conf.'%';
                @endphp
                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">{{ $desa }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ number_format($cases) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $clr }}">{{ number_format($rate, 1) }}%</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $badge }}">{{ $label }}</span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="px-6 py-4 border-t text-sm text-gray-600">
          {{ $hotspots->links('pagination.red') }}
        </div>
      </div>
    </section>

    {{-- ===== Tab: MAP ===== --}}
    <section id="tab-map" class="{{ $currentView==='map' ? '' : 'hidden' }}">
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
        <div class="px-6 py-5 bg-white flex items-center justify-between">
          <h2 class="text-lg font-semibold text-gray-800">Peta Analisis Hotspot</h2>
          <div class="flex flex-wrap gap-2 md:gap-6 items-center">
            <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-red-600"></div><span class="text-sm">99%</span></div>
            <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-orange-600"></div><span class="text-sm">95%</span></div>
            <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-yellow-400"></div><span class="text-sm">90%</span></div>
            <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-gray-300"></div><span class="text-sm">Not Sig.</span></div>
          </div>
        </div>
        <div id="hotspot-map" style="height:600px;width:100%"></div>
      </div>
    </section>
  </div>

  @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script>
      // Dataset lengkap untuk peta (tidak dibatasi pagination)
      const hotspotsAll = @json($datasetAll ?? []);
      const fmt = (n) => (n ?? 0).toLocaleString('id-ID');

      document.addEventListener('DOMContentLoaded', () => {
        // Inisiasi map hanya jika kontainer ada (tab map aktif)
        const mapEl = document.getElementById('hotspot-map');
        if (!mapEl) return;

        const map = L.map('hotspot-map').setView([-7.3167,107.5833], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'© OpenStreetMap' }).addTo(map);

        const bounds = [];
        hotspotsAll.forEach(h => {
          if (h.lat === null || h.lng === null) return; // skip jika belum ada koordinat

          const color = h.confidence===99 ? '#dc2626'
                      : h.confidence===95 ? '#ea580c'
                      : h.confidence===90 ? '#facc15' : '#16a34a';

          const radius = h.confidence>0 ? 15 : 10;
          const opacity = h.confidence>0 ? 0.8 : 0.8;

          const popupHtml = `
            <div class="p-2">
              <strong class="text-gray-800">${h.desa ?? h.name}</strong><br>
              <span class="text-gray-600">
                Confidence: ${h.confidence > 0 ? h.confidence + '%' : 'Not Significant'}<br>
                Populasi: ${fmt(h.population)}<br>
                Kasus: ${fmt(h.cases)}
              </span>
            </div>
          `;

          // marker
          L.circleMarker([h.lat, h.lng], {
            radius, fillColor: color, color:'#fff', weight:2, opacity:1, fillOpacity:opacity
          }).addTo(map).bindPopup(popupHtml);

          // heat zone
          const r = h.confidence >= 99 ? 600
                  : h.confidence >= 95 ? 400
                  : h.confidence >= 90 ? 200
                  : 150;
          L.circle([h.lat,h.lng], {
            radius: r, fillColor: color, color: color,
            weight: 1, opacity: .3, fillOpacity: .1
          }).addTo(map).bindPopup(popupHtml);

          bounds.push([h.lat,h.lng]);
        });

        if (bounds.length) map.fitBounds(bounds,{padding:[20,20]});
      });

      // Auto-submit filter saat ganti period
      document.addEventListener('DOMContentLoaded', () => {
        const f = document.getElementById('filterForm');
        const period = f?.querySelector('input[name="period"]');
        if (f && period) period.addEventListener('change', () => f.submit());
      });
    </script>

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
  @endpush
</x-layout>
