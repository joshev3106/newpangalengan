<x-layout>
  @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
    <style>
      .hero-pattern{
        background-image:
          radial-gradient(24rem 24rem at 10% 10%, rgba(255,255,255,.06), transparent 40%),
          radial-gradient(18rem 18rem at 90% 20%, rgba(255,255,255,.06), transparent 45%),
          radial-gradient(30rem 30rem at 30% 120%, rgba(255,255,255,.05), transparent 45%);
      }
      .glass{ 
        background:rgba(255,255,255,.14); 
        border:1px solid rgba(255,255,255,.2); 
        backdrop-filter:blur(8px); 
      }
      
      /* Custom responsive utilities */
      .card-responsive {
        min-width: 280px;
        flex: 1 1 280px;
      }
      
      .stats-card {
        min-width: 150px;
        flex: 1 1 calc(50% - 0.5rem);
      }
      
      @media (min-width: 768px) {
        .stats-card {
          flex: 1 1 calc(33.333% - 0.67rem);
        }
      }
      
      @media (min-width: 1024px) {
        .stats-card {
          flex: 1 1 calc(16.666% - 0.83rem);
        }
      }
    </style>
  @endpush

  {{-- ============== HERO ============== --}}
  <header class="bg-gradient-to-r from-red-700 via-red-600 to-red-700">
    <div class="bg-red-900/20 py-2 w-full">
      <div class="container mx-auto px-4 hidden md:block">
        <p class="text-center text-red-100 text-sm">Sistem Informasi Data Stunting Kecamatan Pangalengan</p>
      </div>
      <div class="container mx-auto px-4 block md:hidden">
        <p class="text-center text-red-100 text-sm">Sistem Informasi Data Stunting</p>
        <p class="text-center text-red-100 text-sm">Kecamatan Pangalengan</p>
      </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 py-6">
      <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
        {{-- Logo + Title --}}
        <div class="flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left">
          <div class="glass rounded-full p-3 flex-shrink-0">
            <img src="{{ asset('img/logo-kab-bandung.png') }}" alt="Logo Kabupaten Bandung" 
                 class="h-16 w-16 object-contain">
          </div>
          <div class="text-white">
            <div class="glass rounded-xl px-4 py-3">
              <p class="text-lg sm:text-xl font-semibold">Kecamatan Pangalengan</p>
              <p class="text-red-100 text-xs sm:text-sm">Kabupaten Bandung • Provinsi Jawa Barat</p>
              <div class="mt-2 flex flex-wrap justify-center sm:justify-start items-center gap-2 text-xs sm:text-sm text-red-100/90">
                <span class="px-2 py-1 rounded-lg bg-white/10 border border-white/10 whitespace-nowrap">
                  {{ number_format($pkCount) }} Faskes
                </span>
                <span class="px-2 py-1 rounded-lg bg-white/10 border border-white/10 whitespace-nowrap">
                  {{ number_format($desaMappedCount) }} Desa Terpetakan
                </span>
              </div>
            </div>
          </div>
        </div>

        {{-- KPI ringkas --}}
        <div class="flex flex-wrap gap-3 w-full lg:w-auto lg:min-w-[480px] justify-center lg:justify-end">
          <div class="glass rounded-2xl p-3 sm:p-4 text-red-50 flex-1 w-full">
            <div class="text-[10px] sm:text-[11px] uppercase tracking-wide text-white/80">Total Desa</div>
            <div class="text-xl sm:text-2xl font-bold mt-1">{{ number_format($stats['total']) }}</div>
          </div>
          <div class="glass rounded-2xl p-3 sm:p-4 text-red-50 flex-1 w-full">
            <div class="text-[10px] sm:text-[11px] uppercase tracking-wide text-white/80">Rata-rata (%)</div>
            <div class="text-xl sm:text-2xl font-bold mt-1">{{ number_format($stats['avg'], 1) }}%</div>
          </div>
          <div class="glass rounded-2xl p-3 sm:p-4 text-red-50 flex-1 w-full">
            <div class="text-[10px] sm:text-[11px] uppercase tracking-wide text-white/80">Faskes</div>
            <div class="text-xl sm:text-2xl font-bold mt-1">{{ number_format($pkCount) }}</div>
          </div>
        </div>
      </div>

      {{-- Distribusi + Filter --}}
      @php
        $t = max(1, (int)($stats['total'] ?? 0));
        $pHigh = round(($stats['high'] ?? 0) / $t * 100);
        $pMed  = round(($stats['medium'] ?? 0) / $t * 100);
        $pLow  = round(($stats['low'] ?? 0) / $t * 100);
        $pNot  = max(0, 100 - $pHigh - $pMed - $pLow);
      @endphp
      <div class="mt-6 flex flex-col-reverse lg:flex-row gap-4 lg:items-center lg:justify-between">
        <div class="flex-1 lg:max-w-2xl">
          <div class="text-xs text-red-100/90 mb-2">Distribusi Tingkat</div>
          <div class="w-full h-3 rounded-full overflow-hidden ring-1 ring-white/20 flex">
            <div class="h-full flex-none bg-red-500" style="width: {{ $pHigh }}%"></div>
            <div class="h-full flex-none bg-orange-500" style="width: {{ $pMed }}%"></div>
            <div class="h-full flex-none bg-green-500" style="width: {{ $pLow }}%"></div>
            <div class="h-full flex-none bg-gray-300" style="width: {{ $pNot }}%"></div>
          </div>
          <div class="mt-2 flex flex-wrap gap-2 sm:gap-3 text-[10px] sm:text-[11px] text-red-50/90">
            <span class="flex items-center gap-1"><i class="w-2 h-2 bg-red-500 inline-block rounded"></i> High {{ $pHigh }}%</span>
            <span class="flex items-center gap-1"><i class="w-2 h-2 bg-orange-500 inline-block rounded"></i> Medium {{ $pMed }}%</span>
            <span class="flex items-center gap-1"><i class="w-2 h-2 bg-green-500 inline-block rounded"></i> Low {{ $pLow }}%</span>
            <span class="flex items-center gap-1"><i class="w-2 h-2 bg-gray-300 inline-block rounded"></i> Not Sig. {{ $pNot }}%</span>
          </div>
        </div>

        <form method="GET" action="{{ route('home') }}" class="glass rounded-xl px-3 py-3 flex flex-wrap sm:flex-nowrap gap-2 items-center w-full lg:w-auto">
          <input type="month" name="period" value="{{ $period ?? '' }}" 
                 class="rounded-lg p-2 bg-white/95 text-gray-800 text-sm w-full focus:outline-none">
          
          <div class="flex w-full gap-1">
            @if(request()->has('period'))
              <a href="{{ route('home') }}" 
                 class="px-3 py-2 rounded-lg bg-white/10 w-full text-center text-white hover:bg-white/20 text-sm whitespace-nowrap">Reset</a>
            @endif
            <button class="px-4 py-2 rounded-lg bg-white w-full text-red-700 font-semibold hover:bg-red-50 text-sm whitespace-nowrap">Terapkan</button>
          </div>
        </form>
      </div>
    </div>
  </header>

  <x-navbar></x-navbar>

  {{-- ============== MAIN ============== --}}
  <div class="w-full mx-auto px-4 sm:px-6 py-6 sm:py-8">
    {{-- Banner periode --}}
    <div class="mb-4 sm:mb-6 text-sm text-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
      @if(!empty($period))
        <div class="flex-1">Menampilkan data periode:
          <span class="font-semibold">{{ $displayPeriodLabel }}</span>
        </div>
      @else
        <div class="flex-1">Menampilkan <span class="font-semibold">data terbaru</span>:
          <span class="font-semibold">{{ $displayPeriodLabel ?? '-' }}</span>.
        </div>
      @endif
    </div>

    {{-- Main Content Flex Layout --}}
    <div class="grid md:grid-cols-2 gap-5 sm:gap-6">
      {{-- Left Column: Top 5 + Chart --}}
      <div class="flex-1 flex flex-col gap-5 sm:gap-6">
        {{-- Top 5 --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100">
          <div class="px-4 sm:px-5 py-4 border-b flex sm:items-center justify-between gap-2">
            <h3 class="font-semibold text-gray-800">Top 5 Desa (berdasarkan %)</h3>
            <span class="text-xs px-3 py-1 rounded-full bg-gray-100 self-start sm:self-auto">
              {{ !empty($period) ? 'Periode: '.$period : 'Terbaru' }}
            </span>
          </div>
          <div class="px-4 sm:px-5 pb-5 pt-3">
            @if($top5->isEmpty())
              <div class="text-gray-500 text-sm">Belum ada data.</div>
            @else
              <div class="overflow-x-auto">
                <table class="w-full text-sm">
                  <thead>
                    <tr class="text-gray-600 border-b">
                      <th class="px-2 sm:px-4 py-2 text-left">#</th>
                      <th class="px-2 sm:px-4 py-2 text-left">Desa</th>
                      <th class="px-2 sm:px-4 py-2 text-left">Kasus</th>
                      <th class="px-2 sm:px-4 py-2 text-left hidden lg:table-cell">Populasi</th>
                      <th class="px-2 sm:px-4 py-2 text-left">Rate</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y">
                    @foreach($top5 as $i => $r)
                      @php
                        $clr = $r->severity === 'high' ? 'bg-red-600 text-white'
                              : ($r->severity === 'medium' ? 'bg-orange-500 text-white' : 'bg-green-500 text-white');
                      @endphp
                      <tr class="hover:bg-gray-50">
                        <td class="px-2 sm:px-4 py-2 font-medium">{{ $i + 1 }}</td>
                        <td class="px-2 sm:px-4 py-2">{{ $r->desa }}</td>
                        <td class="px-2 sm:px-4 py-2">{{ number_format($r->kasus) }}</td>
                        <td class="px-2 sm:px-4 py-2 hidden lg:table-cell">{{ number_format($r->populasi) }}</td>
                        <td class="px-2 sm:px-4 py-2">
                          <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $clr }}">
                            {{ number_format($r->rate, 1) }}%
                          </span>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>
        </div>

        {{-- Chart --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100">
          <div class="px-4 sm:px-5 py-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <h3 class="font-semibold text-gray-800">Tren Rata-rata 12 Bulan</h3>
            <span class="text-xs text-gray-500 self-start sm:self-auto">{{ $displayPeriodLabel }}</span>
          </div>
          <div class="p-4 sm:p-5">
            <div class="w-full" style="height:300px;">
              <canvas id="trendMini" class="w-full h-full"></canvas>
            </div>
            <div class="mt-3 text-xs text-gray-500">Sumber: Data agregat seluruh desa per bulan.</div>
          </div>
        </div>
      </div>

      {{-- Right Column: Map + Quick Links --}}
      <div class="flex-1 xl:max-w-lg flex flex-col gap-5 sm:gap-6">
        {{-- Peta Faskes --}}
        @php
          $pkMarkers = collect(config('desa_puskesmas.pk_coords', []))->map(function($v,$k){
            return ['name'=>$k,'lat'=>$v['lat']??null,'lng'=>$v['lng']??null,'tipe'=>$v['tipe']??null,'address'=>$v['address']??null];
          })->values();
        @endphp
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100">
          <div class="px-4 sm:px-5 py-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <h3 class="font-semibold text-gray-800">Peta Fasilitas Kesehatan</h3>
            <span class="text-xs text-gray-500 self-start sm:self-auto">{{ number_format($pkCount) }} Faskes</span>
          </div>
          <div class="p-4 sm:p-5">
            <div id="home-pk-map" class="w-full rounded-xl overflow-hidden ring-1 ring-gray-100" 
                 style="height:300px;"></div>
            <div class="mt-2 text-[11px] text-gray-500">Peta Fasilitas Kesehatan Kec. Pangalengan</div>
          </div>
        </div>

        {{-- Quick Links --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100">
          <div class="px-4 sm:px-5 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Navigasi Cepat</h3>
          </div>
          <div class="p-4 sm:p-5 flex flex-col gap-3">
            <a href="{{ route('stunting.index') }}" 
               class="group flex items-center justify-between px-4 py-3 rounded-xl ring-1 ring-gray-200 hover:bg-gray-50 transition">
              <div>
                <div class="font-medium">Data Stunting</div>
                <div class="text-xs text-gray-500">Tabel & Grafik</div>
              </div>
              <span class="text-gray-400 group-hover:translate-x-1 transition">→</span>
            </a>
            <a href="{{ route('hotspot.index') }}" 
               class="group flex items-center justify-between px-4 py-3 rounded-xl ring-1 ring-gray-200 hover:bg-gray-50 transition">
              <div>
                <div class="font-medium">Analisis Hotspot</div>
                <div class="text-xs text-gray-500">Klaster & Peta</div>
              </div>
              <span class="text-gray-400 group-hover:translate-x-1 transition">→</span>
            </a>
            <a href="{{ route('wilayah.index') }}" 
               class="group flex items-center justify-between px-4 py-3 rounded-xl ring-1 ring-gray-200 hover:bg-gray-50 transition">
              <div>
                <div class="font-medium">Data Wilayah</div>
                <div class="text-xs text-gray-500">Profil Desa & Faskes</div>
              </div>
              <span class="text-gray-400 group-hover:translate-x-1 transition">→</span>
            </a>
            <a href="{{ route('peta') ?? '#' }}" 
               class="group flex items-center justify-between px-4 py-3 rounded-xl ring-1 ring-gray-200 hover:bg-gray-50 transition">
              <div>
                <div class="font-medium">Peta Faskes</div>
                <div class="text-xs text-gray-500">{{ number_format($pkCount) }} Faskes • {{ number_format($desaMappedCount) }} desa</div>
              </div>
              <span class="text-gray-400 group-hover:translate-x-1 transition">→</span>
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- Statistics Cards --}}
    <div class=" grid grid-cols-3 mt-6 sm:mt-8 gap-3 sm:gap-4">
      <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm ring-1 ring-gray-100 md:stats-card">
        <div class="text-xs text-gray-500 flex flex-col md:flex-row">
          <span>High</span>
          <span>(&gt;20%)</span>
        </div>
        <div class="text-xl sm:text-2xl font-bold text-red-600">{{ number_format($stats['high']) }}</div>
      </div>
      <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm ring-1 ring-gray-100 md:stats-card">
        <div class="text-xs text-gray-500 flex flex-col md:flex-row">
          <span>Medium</span>
          <span>(10%-20%)</span>
        </div>
        <div class="text-xl sm:text-2xl font-bold text-orange-500">{{ number_format($stats['medium']) }}</div>
      </div>
      <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm ring-1 ring-gray-100 md:stats-card">
        <div class="text-xs text-gray-500 flex flex-col md:flex-row">
          <span>Low</span>
          <span>(&lt;10%)</span>
        </div>
        <div class="text-xl sm:text-2xl font-bold text-green-600">{{ number_format($stats['low']) }}</div>
      </div>
      <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm ring-1 ring-gray-100 md:stats-card">
        <div class="text-xs text-gray-500 flex flex-col md:flex-row">
          <span>Not</span>
          <span>Significant</span>
        </div>
        <div class="text-xl sm:text-2xl font-bold text-gray-700">{{ number_format($stats['not']) }}</div>
      </div>
      <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm ring-1 ring-gray-100 md:stats-card">
        <div class="text-xs text-gray-500 flex flex-col md:flex-row">
          <span>Total</span>
          <span>Desa</span>
        </div>
        <div class="text-xl sm:text-2xl font-bold">{{ number_format($stats['total']) }}</div>
      </div>
      <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm ring-1 ring-gray-100 md:stats-card">
        <div class="text-xs text-gray-500 flex flex-col md:flex-row">
          <span>Rata-rata</span>
          <span>Stunting</span>
        </div>
        <div class="text-xl sm:text-2xl font-bold">{{ number_format($stats['avg'],1) }}%</div>
      </div>
    </div>
  </div>

  @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
    <script>
      // ---- Trend 12 bulan (ambil dari endpoint yang pakai helper yang sama)
      (function(){
        const el = document.getElementById('trendMini'); 
        if (!el) return;
        
        const url = new URL(@json(route('stunting.chart'))), p = @json($period ?? null);
        if (p) url.searchParams.set('period', p);
        
        fetch(url).then(r => r.json()).then(json => {
          new Chart(el.getContext('2d'), {
            type: 'line',
            data: {
              labels: (json.periods ?? []).map(p => p.replace('-', '–')),
              datasets: [{ 
                label:'Rata-rata (%)', 
                data: json.trend ?? [], 
                fill:true, 
                pointRadius:2, 
                tension:.25,
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderColor: 'rgba(239, 68, 68, 1)'
              }]
            },
            options: { 
              responsive:true, 
              maintainAspectRatio:false, 
              plugins:{ legend:{ display:false } },
              scales:{ 
                y:{ beginAtZero:true, ticks:{ callback:v=>v+'%' } },
                x:{ ticks: { maxRotation: 45 } }
              } 
            }
          });
        }).catch(()=>{});
      })();

      // ---- Peta faskes
      (function(){
        const el = document.getElementById('home-pk-map'); 
        if (!el) return;

        const defaultIcon = L.icon({
          iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
          iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
          shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
          iconSize: [25,41], iconAnchor: [12,41], popupAnchor: [1,-34], shadowSize: [41,41]
        });

        const markers = @json($pkMarkers);
        const map = L.map('home-pk-map').setView([-7.3167,107.5833], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
          attribution:'© OpenStreetMap' 
        }).addTo(map);

        const bounds = [];
        markers.forEach(m => {
          if (m.lat == null || m.lng == null) return;
          L.marker([m.lat, m.lng], { icon: defaultIcon })
            .addTo(map)
            .bindPopup(
              `<div class="p-2">
                <div class="font-semibold text-gray-800 text-sm">${m.name}</div>
                <div class="text-xs text-gray-600 mt-1">Tipe: ${m.tipe ?? '-'}</div>
                <div class="text-xs text-gray-600">${m.address ?? ''}</div>
                <div class="mt-2">
                  <a href="https://www.google.com/maps?q=${m.lat},${m.lng}" 
                     target="_blank" rel="noopener" 
                     class="text-blue-600 hover:underline text-xs">
                    Buka di Google Maps
                  </a>
                </div>
              </div>`
            );
          bounds.push([m.lat, m.lng]);
        });

        if (bounds.length) map.fitBounds(bounds, { padding:[20,20] });
        
        // Handle resize
        const invalidate = () => map.invalidateSize();
        window.addEventListener('resize', invalidate);
        setTimeout(invalidate, 100);
      })();
    </script>
  @endpush
</x-layout>
