<x-layout>
    @push('styles')
        <style>
            .table-scroll { overflow: auto; }
            th.sticky { position: sticky; top: 0; background: #fff; z-index: 5; }
        </style>
    @endpush>

    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Data Stunting</h1>
                <p class="text-gray-600">Daftar ringkas kasus per desa. Filter & ekspor cepat.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="#" class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800">+ Tambah Data</a>
                <a href="#" class="px-4 py-2 rounded-lg bg-white ring-1 ring-gray-200 hover:bg-gray-50">↥ Import CSV</a>
                <a href="#" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-500">↧ Export CSV</a>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-4 md:p-5 mb-6">
            <div class="grid md:grid-cols-4 gap-3">
                <div class="relative">
                    <input id="q" type="text" placeholder="Cari desa / ket…"
                           class="w-full rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 pl-10">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
                    </svg>
                </div>
                <select id="severity" class="rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500">
                    <option value="">Semua tingkat</option>
                    <option value="high">Tinggi (&gt;20%)</option>
                    <option value="medium">Sedang (10–20%)</option>
                    <option value="low">Rendah (&lt;10%)</option>
                </select>
                <input id="dateFrom" type="month" class="rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500">
                <input id="dateTo" type="month" class="rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500">
            </div>
        </div>

        {{-- Tabel --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100">
            <div class="table-scroll">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="text-gray-600 border-b">
                            <th class="sticky px-4 py-3 font-semibold">Desa</th>
                            <th class="sticky px-4 py-3 font-semibold">Kasus</th>
                            <th class="sticky px-4 py-3 font-semibold">Populasi</th>
                            <th class="sticky px-4 py-3 font-semibold">Tingkat</th>
                            <th class="sticky px-4 py-3 font-semibold">Update</th>
                        </tr>
                    </thead>
                    <tbody id="rows" class="divide-y">
                        @php
                            $data = [
                                ['desa'=>'Margamulya','kasus'=>45,'pop'=>180,'rate'=>25.0,'sev'=>'high','update'=>'2025-08'],
                                ['desa'=>'Warnasari','kasus'=>32,'pop'=>190,'rate'=>16.8,'sev'=>'medium','update'=>'2025-08'],
                                ['desa'=>'Pangalengan','kasus'=>28,'pop'=>220,'rate'=>12.7,'sev'=>'medium','update'=>'2025-08'],
                                ['desa'=>'Tribaktimulya','kasus'=>9,'pop'=>210,'rate'=>4.3,'sev'=>'low','update'=>'2025-08'],
                                ['desa'=>'Pulosari','kasus'=>14,'pop'=>200,'rate'=>7.0,'sev'=>'low','update'=>'2025-08'],
                            ];
                        @endphp
                        @foreach ($data as $i => $d)
                            <tr class="hover:bg-gray-50" data-desa="{{ Str::lower($d['desa']) }}"
                                data-sev="{{ $d['sev'] }}" data-update="{{ $d['update'] }}">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $d['desa'] }}</td>
                                <td class="px-4 py-3">{{ $d['kasus'] }}</td>
                                <td class="px-4 py-3">{{ number_format($d['pop']) }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $clr = $d['sev']=='high'?'bg-red-600 text-white':
                                               ($d['sev']=='medium'?'bg-orange-500 text-white':'bg-green-500 text-white');
                                    @endphp
                                    <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $clr }}">
                                        {{ number_format($d['rate'],1) }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $d['update'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer / pagination dummy --}}
            <div class="px-4 py-3 border-t flex items-center justify-between text-sm text-gray-600">
                <div><span id="visibleCount"></span> dari <span id="totalCount"></span> baris</div>
                <div class="flex gap-1">
                    <button class="px-3 py-1 rounded-lg hover:bg-gray-100" id="prev">‹</button>
                    <button class="px-3 py-1 rounded-lg hover:bg-gray-100" id="next">›</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Client-side filter & mini-pagination (dummy)
            const q = document.getElementById('q');
            const sev = document.getElementById('severity');
            const from = document.getElementById('dateFrom');
            const to = document.getElementById('dateTo');
            const rows = Array.from(document.querySelectorAll('#rows tr'));
            const totalCount = document.getElementById('totalCount');
            const visibleCount = document.getElementById('visibleCount');
            const pageSize = 8; let page = 1;

            function applyFilter() {
                const key = (q.value || '').toLowerCase();
                const sevVal = sev.value;
                const f = from.value; const t = to.value;

                rows.forEach(r => {
                    const matchText = r.dataset.desa.includes(key);
                    const matchSev = !sevVal || r.dataset.sev === sevVal;
                    const upd = r.dataset.update;
                    const matchDate = (!f || upd >= f) && (!t || upd <= t);
                    r.classList.toggle('hidden', !(matchText && matchSev && matchDate));
                });
                paginate(1);
            }

            function paginate(p) {
                page = p;
                const visibles = rows.filter(r => !r.classList.contains('hidden'));
                totalCount.textContent = rows.length;
                visibleCount.textContent = visibles.length;

                visibles.forEach((r,i) => {
                    const inPage = i >= (page-1)*pageSize && i < page*pageSize;
                    r.style.display = inPage ? '' : 'none';
                });
            }
            q.addEventListener('input', applyFilter);
            sev.addEventListener('change', applyFilter);
            from.addEventListener('change', applyFilter);
            to.addEventListener('change', applyFilter);
            document.getElementById('prev').onclick = () => paginate(Math.max(1, page-1));
            document.getElementById('next').onclick = () => paginate(page+1);

            applyFilter();
        </script>
    @endpush
</x-layout>
