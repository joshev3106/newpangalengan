<x-layout>
    <header class="bg-gradient-to-r from-red-700 via-red-600 to-red-700 shadow-lg">
      <div class="bg-red-900/20 py-2 w-full" id="header">
        <div class="container mx-auto px-4 hidden md:block">
          <p class="text-center text-red-100 text-sm">
            Sistem Informasi Data Stunting Kecamatan Pangalengan
          </p>
        </div>
        <div class="container mx-auto px-4 block md:hidden">
          <p class="text-center text-red-100 text-sm">Sistem Informasi Data Stunting</p>
          <p class="text-center text-red-100 text-sm">Kecamatan Pangalengan</p>
        </div>
      </div>

      <div class="container mx-auto px-4 sm:px-6 py-4 sm:py-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 md:gap-6 ">
          {{-- Logo + Title --}}
          <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-6 text-center sm:text-left">
            <div class="bg-red-700 backdrop-blur-sm rounded-full p-2 sm:p-3 border border-red-600">
              <img src="{{ asset('img/logo-kab-bandung.png') }}" alt="Logo Kabupaten Bandung"
                   class="h-12 w-12 sm:h-16 sm:w-16 object-contain">
            </div>
            <div class="text-white">
              <div class="bg-red-700 backdrop-blur-sm rounded-lg px-3 py-2 sm:px-4 sm:py-3 border border-red-600">
                <p class="text-base sm:text-lg font-semibold">Kecamatan Pangalengan</p>
                <p class="text-red-200 text-xs sm:text-sm">Kabupaten Bandung</p>
                <p class="text-red-200 text-xs sm:text-sm">Provinsi Jawa Barat</p>
              </div>
            </div>
          </div>

          {{-- Stats cards (match home) --}}
          <div class="grid grid-cols-3 gap-2">
              <div class="bg-red-700 rounded-2xl p-5 shadow-sm ring-1 ring-red-600">
                <div class="text-sm text-gray-200">Total Desa</div>
                <div class="text-2xl md:text-3xl font-bold text-white">{{ number_format($stats['total']) }}</div>
              </div>
          
              <div class="bg-red-700 rounded-2xl p-5 shadow-sm ring-1 ring-red-600">
                <div class="text-sm text-gray-200">Rata-rata (%)</div>
                <div class="text-2xl md:text-3xl font-bold text-white">{{ number_format($stats['avg'], 1) }}%</div>
              </div>
          
              <div class="bg-red-700 rounded-2xl p-5 shadow-sm ring-1 ring-red-600">
                <div class="text-sm text-gray-200">Puskesmas Terdata</div>
                <div class="text-2xl md:text-3xl font-bold text-white">{{ number_format($pkCount) }}</div>
              </div>
            </div>
        </div>
      </div>
    </header>

    <x-navbar></x-navbar>

  <div class="max-w-7xl mx-auto px-6 py-8">

    {{-- TITLE / HERO --}}
    
    {{-- BANNER PERIODE --}}
    <div class="mb-4 text-sm text-gray-700 flex flex-col-reverse md:flex-row justify-between">
        @if(!empty($period))
            <div>
                Menampilkan data periode:
                <span class="font-semibold">{{ \Illuminate\Support\Str::of($period)->replace('-', '–') }}</span>
                <span class="text-gray-500"> ({{ $displayPeriodLabel }})</span>
            </div>
        @else
            <div>
                Menampilkan <span class="font-semibold">data terbaru</span>:
                <span class="font-semibold">{{ $displayPeriodLabel ?? '-' }}</span>.
            </div>
        @endif
        
        <div class="flex flex-col gap-2 md:flex-row w-full md:w-auto mb-4 md:mb-0">
          {{-- Filter Periode (opsional) --}}
          <form method="GET" action="{{ route('home') }}" class="flex gap-2 items-center w-full md:w-auto">
            <input
              type="month"
              name="period"
              value="{{ $period ?? '' }}"
              class="rounded-xl p-2 border w-full md:w-auto border-gray-200 focus:border-red-500 focus:ring-red-500"
            >
            @if(request()->has('period'))
              <a href="{{ route('home') }}"
                 class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">Reset</a>
            @endif
            <button class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-500">Terapkan</button>
          </form>
        </div>
    </div>

    {{-- ROW: TOP 5 & QUICK LINKS --}}
    <div class="p-2 w-full">

        <div class="grid md:grid-cols-2 gap-6">
          {{-- TOP 5 DESA --}}
          <div class=" bg-white rounded-2xl shadow-sm ring-1 ring-gray-100">
            <div class="px-5 py-4 border-b flex items-center justify-between">
              <h3 class="font-semibold text-gray-800">Top 5 Desa (berdasarkan %)</h3>
              @if(!empty($period))
                <span class="text-xs px-3 py-1 rounded-full bg-gray-100">Periode: {{ $period }}</span>
              @else
                <span class="text-xs px-3 py-1 rounded-full bg-gray-100">Terbaru</span>
              @endif
            </div>
            <div class="p-5">
              @if($top5->isEmpty())
                <div class="text-gray-500 text-sm">Belum ada data.</div>
              @else
                <div class="overflow-x-auto">
                  <table class="w-full text-sm">
                    <thead>
                      <tr class="text-gray-600 border-b">
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Desa</th>
                        <th class="px-4 py-2 text-left">Kasus</th>
                        <th class="px-4 py-2 text-left hidden md:block">Populasi</th>
                        <th class="px-4 py-2 text-left">Rate</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y">
                      @foreach($top5 as $i => $r)
                        @php
                          $clr = $r->severity === 'high' ? 'bg-red-600 text-white'
                              : ($r->severity === 'medium' ? 'bg-orange-500 text-white'
                              : 'bg-green-500 text-white');
                        @endphp
                        <tr class="hover:bg-gray-50">
                          <td class="px-4 py-2 font-medium">{{ $i + 1 }}</td>
                          <td class="px-4 py-2">{{ $r->desa }}</td>
                          <td class="px-4 py-2">{{ number_format($r->kasus) }}</td>
                          <td class="px-4 py-2 hidden md:block">{{ number_format($r->populasi) }}</td>
                          <td class="px-4 py-2">
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
    
          {{-- QUICK LINKS --}}
          <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Navigasi Cepat</h3>
            <div class="space-y-3">
              <a href="{{ route('stunting.index') }}"
                 class="flex items-center justify-between px-4 py-3 rounded-xl ring-1 ring-gray-200 hover:bg-gray-50">
                <div>
                  <div class="font-medium">Data Stunting</div>
                  <div class="text-xs text-gray-500">Analisis per desa</div>
                </div>
                <span class="text-gray-400">→</span>
              </a>
    
              <a href="{{ route('hotspot.index') }}"
                 class="flex items-center justify-between px-4 py-3 rounded-xl ring-1 ring-gray-200 hover:bg-gray-50">
                <div>
                  <div class="font-medium">Analisis Hotspot</div>
                  <div class="text-xs text-gray-500">Klaster & peta analisis</div>
                </div>
                <span class="text-gray-400">→</span>
              </a>
    
              <a href="{{ route('wilayah.index') }}"
                 class="flex items-center justify-between px-4 py-3 rounded-xl ring-1 ring-gray-200 hover:bg-gray-50">
                <div>
                  <div class="font-medium">Data Wilayah</div>
                  <div class="text-xs text-gray-500">Profil desa & faskes</div>
                </div>
                <span class="text-gray-400">→</span>
              </a>
    
              <a href="{{ route('peta') ?? '#' }}"
                 class="flex items-center justify-between px-4 py-3 rounded-xl ring-1 ring-gray-200 hover:bg-gray-50">
                <div>
                  <div class="font-medium">Peta Faskes</div>
                  <div class="text-xs text-gray-500">
                    {{ number_format($pkCount) }} puskesmas • {{ number_format($desaMappedCount) }} desa terpetakan
                  </div>
                </div>
                <span class="text-gray-400">→</span>
              </a>
            </div>
          </div>
        </div>
    </div>

  </div>
</x-layout>
