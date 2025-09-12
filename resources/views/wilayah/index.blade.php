<x-layout>
    <div class="max-w-7xl mx-auto px-6 py-8" x-data="wilayahPage()">

        {{-- Flash / Error --}}
        @if (session('ok'))
            <div class="mb-4 rounded-lg bg-green-50 text-green-700 px-4 py-3">{{ session('ok') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 text-red-700 px-4 py-3">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- Filter: Desa + Rentang Periode --}}
        <div class="item-center bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-4 md:p-5 mb-4">
          <form method="GET" action="{{ route('wilayah.index') }}" id="wilayahFilter"
                x-data="{ desa: @entangle('desa').defer ?? '{{ request('desa','') }}' }"
                class="flex flex-col md:flex-row gap-2 md:items-center justify-between">
              
            <input type="text" name="desa" x-model="desa"
                   placeholder="Cari Desa"
                   class="rounded-lg w-full border border-gray-300 px-3 py-2">
              
            <div class="flex flex-col md:flex-row gap-2">
              <div class="flex gap-1">
                <input type="month" name="start" value="{{ request('start') }}"
                       :disabled="!desa"
                       class="rounded-lg min-w-1 md:w-full border border-gray-300 px-3 py-2 disabled:bg-gray-100">
                <span class="self-center text-gray-500">s/d</span>
                <input type="month" name="end" value="{{ request('end') }}"
                       :disabled="!desa"
                       class="rounded-lg min-w-1 md:w-full border border-gray-300 px-3 py-2 disabled:bg-gray-100">
              </div>
  
              <div class="flex gap-2 w-full md:w-auto">
                <button class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-500 w-full md:w-auto">
                  Terapkan
                </button>
                @if(request()->has('desa') || request()->has('start') || request()->has('end'))
                  <a href="{{ route('wilayah.index') }}"
                     class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-center w-full md:w-auto">
                    Reset
                  </a>
                @endif
              </div>
            </div>
          </form>
        </div>

        {{-- Banner periode / default --}}
        @if(!empty($rangeLabel))
          <div class="mb-3 text-sm text-gray-600">
            Menampilkan <span class="font-semibold">{{ $desa ?? request('desa') }}</span> pada periode:
            <span class="font-semibold">{{ $rangeLabel }}</span>
          </div>
        @else
          <div class="mb-3 text-sm text-gray-600">
            Menampilkan <span class="font-semibold">data terbaru</span>
            <span class="font-semibold">{{ $displayPeriodLabel ?? '-' }}</span>.
          </div>
        @endif

        {{-- Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100">
                <div class="text-sm text-gray-500">Total Desa</div>
                <div class="text-2xl md:text-3xl font-bold">{{ $rows->count() }}</div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100">
                <div class="text-sm text-gray-500">Rata-rata Stunting</div>
                @php
                    $avgRate = $rows->avg(function($r){ return $r->populasi>0 ? ($r->kasus/$r->populasi*100) : 0; });
                @endphp
                <div class="text-2xl md:text-3xl font-bold">{{ number_format($avgRate,1) }}%</div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100">
                <div class="text-sm text-gray-500">Cakupan Faskes (avg)</div>
                @php $avgCov = $rows->avg(fn($r)=> $r->cakupan ?? 0); @endphp
                <div class="text-2xl md:text-3xl font-bold">{{ number_format($avgCov,0) }}%</div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100">
                <div class="text-sm text-gray-500">Update Terakhir</div>
                <div class="text-2xl md:text-3xl font-bold">
                  {{ $lastUpdateLabel ?? '-' }}
                </div>
            </div>
        </div>

        {{-- Daftar Wilayah --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100">
            <div class="overflow-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="text-gray-600 border-b">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Desa</th>
                            <th class="px-4 py-3 font-semibold">Populasi</th>
                            <th class="px-4 py-3 font-semibold">Stunting (%) </th>
                            @if(!empty($rangeLabel))
                              <th class="px-4 py-3 font-semibold">Periode</th>
                            @endif
                            <th class="px-4 py-3 font-semibold">Faskes Terdekat</th>
                            <th class="px-4 py-3 font-semibold">Cakupan</th>
                            <th class="px-4 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="wilRows" class="divide-y">
                        @forelse ($rows as $r)
                            @php
                                $rate = $r->populasi > 0 ? round(($r->kasus / $r->populasi) * 100, 1) : 0;
                                $sevRow = $rate > 20 ? 'high' : ($rate >= 10 ? 'medium' : 'low');
                                $clr = $sevRow=='high'?'bg-red-600 text-white':($sevRow=='medium'?'bg-orange-500 text-white':'bg-green-500 text-white');
                                $periodText = \Illuminate\Support\Carbon::parse($r->period)->isoFormat("MMM 'YY");
                            @endphp
                            <tr class="hover:bg-gray-50"
                                x-show="filterRow('{{ Str::lower($r->desa) }}', '{{ $sevRow }}')">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $r->desa }}</td>
                                <td class="px-4 py-3">{{ number_format($r->populasi) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $clr }}">
                                        {{ number_format($rate,1) }}%
                                    </span>
                                </td>
                                @if(!empty($rangeLabel))
                                  <td class="px-4 py-3">{{ $periodText }}</td>
                                @endif
                                <td class="px-4 py-3">{{ $r->faskes_nama ?: '—' }}</td>
                                <td class="px-4 py-3">{{ $r->cakupan !== null ? $r->cakupan.'%' : '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        {{-- Tombol LIHAT PETA: buka modal & popup pada faskes sesuai config --}}
                                        <a href="javascript:void(0)"
                                           @click="showMap('{{ e($r->desa) }}')"
                                           class="px-3 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-500 hover:cursor-pointer">
                                            Lihat Peta
                                        </a>

                                        @auth
                                        <button type="button"
                                                class="px-3 py-1.5 rounded-lg bg-white ring-1 ring-gray-200 hover:bg-gray-50 hover:cursor-pointer"
                                                @click="openEdit('{{ e($r->desa) }}', '{{ e($r->faskes) }}', {{ $r->cakupan ?? 'null' }})">
                                            Edit Profil
                                        </button>
                                        @endauth
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t text-sm text-gray-600">
                <span x-text="visibleCount()"></span> desa ditampilkan
            </div>
        </div>

        {{-- Modal Edit Profil (Alpine.js) --}}
        @auth
        <div x-show="modalOpen" x-cloak
             class="fixed inset-0 bg-black/40 grid place-items-center p-4">
            <div @click.outside="modalOpen=false"
                 class="w-full max-w-md bg-white rounded-2xl p-5 shadow-xl">
                <h2 class="text-lg font-semibold mb-3">Edit Profil Desa</h2>
                <form method="POST" action="{{ route('wilayah.upsert') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="desa" :value="form.desa">

                    <div>
                        <label class="block text-sm font-medium mb-1">Desa</label>
                        <input type="text" class="w-full rounded-xl p-2 border border-gray-200 bg-gray-100" :value="form.desa" disabled>
                    </div>

                    <div>
                      <label class="block text-sm font-medium mb-1">Faskes</label>
                      <input type="text" name="faskes" x-model="form.faskes"
                             placeholder="Contoh: Puskesmas Pangalengan"
                             class="w-full rounded-xl p-2 border border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Cakupan (%)</label>
                        <input type="number" name="cakupan" x-model="form.cakupan" min="0" max="100"
                               class="w-full rounded-xl p-2 border border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="modalOpen=false"
                                class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 hover:cursor-pointer">Batal</button>
                        <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 hover:cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        @endauth

        {{-- ================== MODAL PETA (Leaflet) ================== --}}
        <div x-show="mapOpen" x-cloak class="fixed inset-0 bg-black/40 grid place-items-center p-4 z-50">
          <div @click.outside="closeMap()"
               class="w-full max-w-2xl bg-white rounded-2xl p-4 shadow-xl">
            <div class="flex items-center justify-between mb-2">
              <h2 class="text-lg font-semibold" x-text="mapTitle"></h2>
              <button class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200"
                      @click="closeMap()">Tutup</button>
            </div>
            <div id="miniMap" style="height: 420px; width: 100%;"
                 class="rounded-xl overflow-hidden ring-1 ring-gray-100"></div>
          </div>
        </div>
        {{-- ========================================================== --}}

    </div>

    @push('scripts')
        {{-- Leaflet (untuk modal peta) --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>

        <script>
        // Kirim config PHP → JS
        const DESA_TO_PK = @json(config('desa_puskesmas.desa_to_pk', []));
        const PK_COORDS  = @json(config('desa_puskesmas.pk_coords', []));

        function wilayahPage() {
          return {
            // --- state lama ---
            q:'', sev:'',
            modalOpen:false,
            form:{ desa:'', puskesmas_id:'', faskes:'', cakupan:null },

            // --- state peta ---
            mapOpen:false,
            mapTitle:'Peta Faskes',
            _leaflet:null,
            _marker:null,
        
            openEdit(desa, faskesNama, cakupan) {
              this.form.desa = desa;
              this.form.cakupan = cakupan;
              this.form.puskesmas_id = '';
              this.form.faskes = faskesNama || '';
            
              if (!this.form.faskes) {
                const sel = document.querySelector('select[name="puskesmas_id"]');
                if (sel) {
                  const needle = desa.toLowerCase();
                  for (const opt of sel.options) {
                    if (opt.value && opt.text.toLowerCase().includes(needle)) {
                      this.form.puskesmas_id = opt.value;
                      break;
                    }
                  }
                  if (!this.form.puskesmas_id) {
                    this.form.faskes = `Puskesmas ${desa.replace(/^(desa|kelurahan)\s+/i,'')}`;
                  }
                }
              }
              this.modalOpen = true;
            },
        
            filterRow(desaLower, sevRow) {
              const okText = desaLower.includes((this.q || '').toLowerCase());
              const okSev  = !this.sev || this.sev === sevRow;
              return okText && okSev;
            },
        
            visibleCount() {
              const rows = [...document.querySelectorAll('#wilRows tr')].filter(tr => tr.offsetParent !== null);
              return rows.length;
            },

            // ====== PETA: buka modal & tampilkan popup faskes utk desa ======
            showMap(desa) {
              const pkName = DESA_TO_PK[desa] || null;
              if (!pkName) {
                alert('Desa belum dipetakan ke puskesmas di config/desa_puskesmas.php');
                return;
              }
              const coord = PK_COORDS[pkName] || null;
              if (!coord || coord.lat == null || coord.lng == null) {
                alert(`Koordinat untuk "${pkName}" belum diisi di config/desa_puskesmas.php (pk_coords).`);
                return;
              }

              this.mapTitle = pkName;
              this.mapOpen = true;

              this.$nextTick(() => {
                // reset instance lama agar tidak dobel
                if (this._leaflet) { this._leaflet.remove(); this._leaflet = null; this._marker = null; }

                this._leaflet = L.map('miniMap').setView([coord.lat, coord.lng], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                  attribution: '© OpenStreetMap'
                }).addTo(this._leaflet);

                this._marker = L.marker([coord.lat, coord.lng]).addTo(this._leaflet);

                const addr = (coord.address ?? ''); // opsional: jika ada 'address' di config
                const popupHtml = `<strong>${pkName}</strong>${addr ? '<br><small>'+addr+'</small>' : ''}`;
                this._marker.bindPopup(popupHtml).openPopup();
              });
            },

            closeMap() {
              this.mapOpen = false;
              if (this._leaflet) { this._leaflet.remove(); this._leaflet = null; this._marker = null; }
            }
          }
        }
        </script>
    @endpush
</x-layout>
