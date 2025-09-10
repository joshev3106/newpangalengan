<x-layout>
  @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
    <style>#hotspot-map{min-height:400px}@media(min-width:768px){#hotspot-map{min-height:600px}}</style>
  @endpush

  <div class="max-w-7xl mx-auto p-6">
    @if(session('ok'))
      <div class="mb-4 rounded-lg bg-green-50 text-green-700 px-4 py-3">{{ session('ok') }}</div>
    @endif
    @if ($errors->any())
      <div class="mb-4 rounded-lg bg-red-50 text-red-700 px-4 py-3">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    {{-- Overview Cards --}}
    <div class="grid md:grid-cols-3 gap-6 mb-6">
      <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-red-500">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Analisis Spasial</h3>
        <p class="text-gray-600">Wilayah dengan konsentrasi tinggi kasus stunting berdasarkan analisis spasial (Getis-Ord Gi*).</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-orange-500">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Hotspot Teridentifikasi</h3>
        <p class="text-gray-600">Ditemukan <span id="total-hotspots">{{ $stats['total'] }}</span> cluster hotspot.</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-yellow-500">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Metodologi</h3>
        <p class="text-gray-600">Clustering spasial untuk pola distribusi tidak acak.</p>
      </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-red-600">
        <div class="text-3xl font-bold text-red-600 mb-1" id="high-confidence">{{ $stats['high'] }}</div>
        <div class="text-sm text-gray-600">High Confidence (99%)</div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-orange-600">
        <div class="text-3xl font-bold text-orange-600 mb-1" id="medium-confidence">{{ $stats['medium'] }}</div>
        <div class="text-sm text-gray-600">Medium Confidence (95%)</div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-yellow-500">
        <div class="text-3xl font-bold text-yellow-600 mb-1" id="low-confidence">{{ $stats['low'] }}</div>
        <div class="text-sm text-gray-600">Low Confidence (90%)</div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-gray-400">
        <div class="text-3xl font-bold text-gray-600 mb-1" id="not-significant">{{ $stats['not'] }}</div>
        <div class="text-sm text-gray-600">Not Significant</div>
      </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-md mb-6">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Data Hotspot Stunting</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Desa</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasus</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Confidence</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($hotspots as $h)
              @php
                  $conf = $h['confidence'];
                  $cases = $h['cases'];
                  $desa = $h['desa'];
                  $badge = $conf===99 ? 'bg-red-100 text-red-800'
                         : ($conf===95 ? 'bg-orange-100 text-orange-800'
                         : ($conf===90 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'));
                  $label = $conf===0 ? 'Not Significant' : $conf.'%';
              @endphp
              <tr>
                <td class="px-6 py-4">{{ $desa }}</td>
                <td class="px-6 py-4">{{ $cases }}</td>
                <td class="px-6 py-4">
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

    {{-- Map --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
      <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Peta Analisis Hotspot</h2>
        <div class="flex flex-wrap gap-6 items-center">
          <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-red-600"></div><span class="text-sm">99%</span></div>
          <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-orange-600"></div><span class="text-sm">95%</span></div>
          <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-yellow-400"></div><span class="text-sm">90%</span></div>
          <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-gray-300"></div><span class="text-sm">Not Sig.</span></div>
        </div>
      </div>
      <div id="hotspot-map" style="height:600px;width:100%"></div>
    </div>
  </div>

  @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script>
      const hotspots = @json($hotspots->items());
      const fmt = (n) => (n ?? 0).toLocaleString('id-ID');

      document.addEventListener('DOMContentLoaded', () => {
        const map = L.map('hotspot-map').setView([-7.3167,107.5833], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'© OpenStreetMap' }).addTo(map);

        const bounds = [];
        hotspots.forEach(h => {
          if (h.lat === null || h.lng === null) return; // skip jika belum ada koordinat

          const color = h.confidence===99 ? '#dc2626'
                      : h.confidence===95 ? '#ea580c'
                      : h.confidence===90 ? '#facc15' : '#d1d5dc';

          const radius = h.confidence>0 ? 15 : 10;
          const opacity = h.confidence>0 ? 0.8 : 0.8;

          const popupHtml = `
            <div class="p-2">
              <strong class="text-gray-800">${h.desa ?? h.name}</strong><br>
              <span class="text-gray-600">
                Populasi: ${fmt(h.population)}<br>
                Confidence: ${h.confidence > 0 ? h.confidence + '%' : 'Not Significant'}<br>
                Kasus: ${fmt(h.cases)}
              </span>
            </div>
          `;

          // marker
          L.circleMarker([h.lat, h.lng], {
            radius, fillColor: color, color:'#fff', weight:2, opacity:1, fillOpacity:opacity
          }).addTo(map).bindPopup(popupHtml);

          // heat zone untuk confidence >= 90
          if (h.confidence >= 0) {
            const r = h.confidence >= 99 ? 800
                    : h.confidence >= 95 ? 600
                    : h.confidence >= 90 ? 400
                    : 250;
            L.circle([h.lat,h.lng], {
              radius: r, fillColor: color, color: color,
              weight: 1, opacity: .3, fillOpacity: .1
            }).addTo(map).bindPopup(popupHtml);
          }

          bounds.push([h.lat,h.lng]);
        });

        if (bounds.length) map.fitBounds(bounds,{padding:[20,20]});
      });
    </script>
  @endpush
</x-layout>
