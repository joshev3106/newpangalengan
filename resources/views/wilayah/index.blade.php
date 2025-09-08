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

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Data Wilayah</h1>
                <p class="text-gray-600">Profil singkat desa: populasi, cakupan faskes, & tingkat stunting.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('peta') ?? '#' }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500">🗺️ Lihat di Peta</a>
                <a href="#" class="px-4 py-2 rounded-lg bg-white ring-1 ring-gray-200 hover:bg-gray-50">↧ Export Excel</a>
            </div>
        </div>

        {{-- Cards dummy (opsional, bisa diisi dari query aggregate) --}}
        <div class="grid md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100">
                <div class="text-sm text-gray-500">Total Desa</div>
                <div class="text-3xl font-bold">{{ $rows->count() }}</div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100">
                <div class="text-sm text-gray-500">Rata-rata Stunting</div>
                @php
                    $avgRate = $rows->avg(function($r){ return $r->populasi>0 ? ($r->kasus/$r->populasi*100) : 0; });
                @endphp
                <div class="text-3xl font-bold">{{ number_format($avgRate,1) }}%</div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100">
                <div class="text-sm text-gray-500">Cakupan Faskes (avg)</div>
                @php $avgCov = $rows->avg(fn($r)=> $r->cakupan ?? 0); @endphp
                <div class="text-3xl font-bold">{{ number_format($avgCov,0) }}%</div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100">
                <div class="text-sm text-gray-500">Update Terakhir</div>
                @php 
                    $last = optional($rows->max('period')); 
                @endphp
                <div class="text-3xl font-bold">
                  {{ $last ? $last->isoFormat("MMM 'YY") : '-' }}
                </div>
            </div>
        </div>

        {{-- Daftar Wilayah --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100">
            <div class="px-4 py-4 border-b flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
                <div class="font-semibold text-gray-800">Daftar Desa</div>
                <div class="flex gap-2">
                    <input id="qWil" type="text" placeholder="Cari desa…" x-model="q"
                           class="rounded-xl p-2 border border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                    <select id="sevWil" x-model="sev" class="hover:cursor-pointer rounded-xl p-2 border border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua tingkat</option>
                        <option value="high">Tinggi</option>
                        <option value="medium">Sedang</option>
                        <option value="low">Rendah</option>
                    </select>
                </div>
            </div>

            <div class="overflow-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="text-gray-600 border-b">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Desa</th>
                            <th class="px-4 py-3 font-semibold">Populasi</th>
                            <th class="px-4 py-3 font-semibold">Stunting</th>
                            <th class="px-4 py-3 font-semibold">Faskes Terdekat</th>
                            <th class="px-4 py-3 font-semibold">Cakupan</th>
                            <th class="px-4 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="wilRows" class="divide-y">
                        @foreach ($rows as $r)
                            @php
                                $rate = $r->populasi > 0 ? round(($r->kasus / $r->populasi) * 100, 1) : 0;
                                $sevRow = $rate > 20 ? 'high' : ($rate >= 10 ? 'medium' : 'low');
                                $clr = $sevRow=='high'?'bg-red-600 text-white':($sevRow=='medium'?'bg-orange-500 text-white':'bg-green-500 text-white');
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
                                <td class="px-4 py-3">{{ $r->faskes ?: '—' }}</td>
                                <td class="px-4 py-3">{{ $r->cakupan !== null ? $r->cakupan.'%' : '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('peta') ?? '#' }}"
                                           class="px-3 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-500 hover:cursor-pointer">Lihat Peta</a>

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
                        @endforeach
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
                        <label class="block text-sm font-medium mb-1">Faskes Terdekat</label>
                        <input type="text" name="faskes" x-model="form.faskes"
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

    </div>

    @push('scripts')
    <script>
    function wilayahPage() {
        return {
            q: '', sev: '',
            modalOpen: false,
            form: { desa: '', faskes: '', cakupan: null },

            openEdit(desa, faskes, cakupan) {
                this.form.desa = desa;
                this.form.faskes = faskes || '';
                this.form.cakupan = cakupan;
                this.modalOpen = true;
            },

            filterRow(desaLower, sevRow) {
                const okText = desaLower.includes((this.q || '').toLowerCase());
                const okSev  = !this.sev || this.sev === sevRow;
                return okText && okSev;
            },

            visibleCount() {
                // hitung row yang lolos filter di sisi klien (kasar)
                const tbody = document.getElementById('wilRows');
                const rows = [...tbody.querySelectorAll('tr')].filter(tr => tr.offsetParent !== null);
                return rows.length;
            }
        }
    }
    </script>
    @endpush
</x-layout>
