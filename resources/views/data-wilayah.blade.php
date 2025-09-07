<x-layout>
    <div class="max-w-7xl mx-auto px-6 py-8">
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

        {{-- Cards ringkas --}}
        <div class="grid md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100">
                <div class="text-sm text-gray-500">Total Desa</div>
                <div class="text-3xl font-bold">35</div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100">
                <div class="text-sm text-gray-500">Rata-rata Stunting</div>
                <div class="text-3xl font-bold">18.5%</div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100">
                <div class="text-sm text-gray-500">Cakupan Faskes</div>
                <div class="text-3xl font-bold">92%</div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100">
                <div class="text-sm text-gray-500">Update Terakhir</div>
                <div class="text-3xl font-bold">Aug ‘25</div>
            </div>
        </div>

        {{-- Daftar Wilayah --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100">
            <div class="px-4 py-4 border-b flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
                <div class="font-semibold text-gray-800">Daftar Desa</div>
                <div class="flex gap-2">
                    <input id="qWil" type="text" placeholder="Cari desa…"
                           class="rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                    <select id="sevWil" class="rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
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
                        @php
                            $wil = [
                                ['desa'=>'Margamulya','pop'=>7420,'rate'=>25.0,'sev'=>'high','faskes'=>'Puskesmas Margamulya','cov'=>88],
                                ['desa'=>'Warnasari','pop'=>6810,'rate'=>16.8,'sev'=>'medium','faskes'=>'Puskesmas Warnasari','cov'=>93],
                                ['desa'=>'Pangalengan','pop'=>9200,'rate'=>12.7,'sev'=>'medium','faskes'=>'Puskesmas Pangalengan','cov'=>97],
                                ['desa'=>'Tribaktimulya','pop'=>5600,'rate'=>4.3,'sev'=>'low','faskes'=>'Posyandu Melati','cov'=>90],
                                ['desa'=>'Pulosari','pop'=>6100,'rate'=>7.0,'sev'=>'low','faskes'=>'Posyandu Mawar','cov'=>91],
                            ];
                        @endphp
                        @foreach ($wil as $w)
                            <tr class="hover:bg-gray-50" data-sev="{{ $w['sev'] }}">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $w['desa'] }}</td>
                                <td class="px-4 py-3">{{ number_format($w['pop']) }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $clr = $w['sev']=='high'?'bg-red-600 text-white':
                                               ($w['sev']=='medium'?'bg-orange-500 text-white':'bg-green-500 text-white');
                                    @endphp
                                    <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $clr }}">{{ number_format($w['rate'],1) }}%</span>
                                </td>
                                <td class="px-4 py-3">{{ $w['faskes'] }}</td>
                                <td class="px-4 py-3">{{ $w['cov'] }}%</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('peta') ?? '#' }}" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-500">Lihat Peta</a>
                                        <a href="#" class="px-3 py-1.5 rounded-lg bg-white ring-1 ring-gray-200 hover:bg-gray-50">Detail</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t text-sm text-gray-600">
                <span id="wilCount"></span> desa ditampilkan
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const qWil = document.getElementById('qWil');
            const sevWil = document.getElementById('sevWil');
            const wilRows = Array.from(document.querySelectorAll('#wilRows tr'));
            const wilCount = document.getElementById('wilCount');

            function filterWil() {
                const qv = (qWil.value || '').toLowerCase();
                const sv = sevWil.value;
                let c = 0;
                wilRows.forEach(r => {
                    const desa = r.querySelector('td').textContent.toLowerCase();
                    const ok = desa.includes(qv) && (!sv || r.dataset.sev === sv);
                    r.classList.toggle('hidden', !ok);
                    if (ok) c++;
                });
                wilCount.textContent = c;
            }
            qWil.addEventListener('input', filterWil);
            sevWil.addEventListener('change', filterWil);
            filterWil();
        </script>
    @endpush
</x-layout>
