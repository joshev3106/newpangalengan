<x-layout>
    @push('styles')
        <style>
            @media print {
                .no-print { display: none !important; }
                .card { break-inside: avoid; }
            }
        </style>
    @endpush

    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Laporan</h1>
                <p class="text-gray-600">Generate ringkasan analisis dan ekspor ke PDF/Excel.</p>
            </div>
            <div class="no-print flex gap-2">
                <button id="btnPrint" class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800">🖨️ Cetak</button>
                <a id="btnPdf" href="#" class="px-4 py-2 rounded-lg bg-white ring-1 ring-gray-200 hover:bg-gray-50">PDF</a>
                <a id="btnXls" href="#" class="px-4 py-2 rounded-lg bg-white ring-1 ring-gray-200 hover:bg-gray-50">Excel</a>
            </div>
        </div>

        {{-- Parameter Laporan --}}
        <div class="no-print bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-4 md:p-5 mb-6">
            <div class="grid md:grid-cols-5 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1">Periode</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input id="lapFrom" type="month" class="rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500">
                        <input id="lapTo" type="month" class="rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Jenis</label>
                    <select id="lapKind" class="rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500">
                        <option value="ringkas">Ringkas</option>
                        <option value="detail">Detail</option>
                        <option value="puskesmas">Jaringan Faskes</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Format</label>
                    <select id="lapFmt" class="rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500">
                        <option value="pdf">PDF</option>
                        <option value="xlsx">Excel</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button id="btnGen" class="w-full px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-500">Generate</button>
                </div>
            </div>
        </div>

        {{-- Preview / Isi Laporan --}}
        <div id="laporanWrap" class="space-y-6">
            {{-- Ringkasan KPI --}}
            <div class="grid md:grid-cols-4 gap-4">
                <div class="card bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100">
                    <div class="text-sm text-gray-500">Rata-rata Stunting</div>
                    <div class="text-3xl font-bold">18.5%</div>
                </div>
                <div class="card bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100">
                    <div class="text-sm text-gray-500">Cluster Hotspot</div>
                    <div class="text-3xl font-bold">5</div>
                </div>
                <div class="card bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100">
                    <div class="text-sm text-gray-500">Faskes Aktif</div>
                    <div class="text-3xl font-bold">12</div>
                </div>
                <div class="card bg-white rounded-2xl p-5 shadow-sm ring-1 ring-gray-100">
                    <div class="text-sm text-gray-500">Update Terakhir</div>
                    <div class="text-3xl font-bold">Aug ‘25</div>
                </div>
            </div>

            {{-- Hotspot Section --}}
            <div class="card bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div class="font-semibold text-gray-800">Hotspot Terkini</div>
                    <a href="{{ route('analisis-hotspot') ?? '#' }}" class="text-sm text-red-700 hover:underline">Lihat Analisis</a>
                </div>
                <div class="mt-4 grid md:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl bg-red-50 ring-1 ring-red-100">
                        <div class="text-sm text-red-800">Confidence 99%</div>
                        <div class="text-2xl font-bold text-red-900">3 Area</div>
                        <div class="text-xs text-red-700 mt-1">Margamulya, sekitarnya</div>
                    </div>
                    <div class="p-4 rounded-xl bg-orange-50 ring-1 ring-orange-100">
                        <div class="text-sm text-orange-800">Confidence 95%</div>
                        <div class="text-2xl font-bold text-orange-900">2 Area</div>
                        <div class="text-xs text-orange-700 mt-1">Warnasari, sekitarnya</div>
                    </div>
                    <div class="p-4 rounded-xl bg-yellow-50 ring-1 ring-yellow-100">
                        <div class="text-sm text-yellow-800">Confidence 90%</div>
                        <div class="text-2xl font-bold text-yellow-900">1 Area</div>
                        <div class="text-xs text-yellow-700 mt-1">Tribaktimulya</div>
                    </div>
                </div>
            </div>

            {{-- Tabel Ringkasan Desa --}}
            <div class="card bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6">
                <div class="font-semibold text-gray-800 mb-4">Ringkasan Per Desa</div>
                <div class="overflow-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="text-gray-600 border-b">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Desa</th>
                                <th class="px-4 py-3 font-semibold">Kasus</th>
                                <th class="px-4 py-3 font-semibold">Populasi</th>
                                <th class="px-4 py-3 font-semibold">Tingkat</th>
                                <th class="px-4 py-3 font-semibold">Faskes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @php
                                $r = [
                                    ['desa'=>'Margamulya','kasus'=>45,'pop'=>180,'rate'=>25.0,'sev'=>'high','f'=>'Puskesmas Margamulya'],
                                    ['desa'=>'Warnasari','kasus'=>32,'pop'=>190,'rate'=>16.8,'sev'=>'medium','f'=>'Puskesmas Warnasari'],
                                    ['desa'=>'Tribaktimulya','kasus'=>9,'pop'=>210,'rate'=>4.3,'sev'=>'low','f'=>'Posyandu Melati'],
                                ];
                            @endphp
                            @foreach ($r as $i)
                                @php
                                    $clr = $i['sev']=='high'?'bg-red-600 text-white':
                                           ($i['sev']=='medium'?'bg-orange-500 text-white':'bg-green-500 text-white');
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $i['desa'] }}</td>
                                    <td class="px-4 py-3">{{ $i['kasus'] }}</td>
                                    <td class="px-4 py-3">{{ $i['pop'] }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $clr }}">{{ number_format($i['rate'],1) }}%</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $i['f'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Rekomendasi --}}
                <div class="mt-6 grid md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-red-50 ring-1 ring-red-100">
                        <div class="font-semibold text-red-900">Prioritas Intervensi</div>
                        <ul class="text-sm text-red-800 mt-2 list-disc ml-5 space-y-1">
                            <li>Perbanyak PMT di area 99%.</li>
                            <li>Monitoring pekanan & kunjungan rumah.</li>
                        </ul>
                    </div>
                    <div class="p-4 rounded-xl bg-blue-50 ring-1 ring-blue-100">
                        <div class="font-semibold text-blue-900">Koordinasi Faskes</div>
                        <ul class="text-sm text-blue-800 mt-2 list-disc ml-5 space-y-1">
                            <li>Sinkronisasi posyandu & puskesmas bulanan.</li>
                            <li>Target edukasi ibu hamil & balita.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="card bg-yellow-50 ring-1 ring-yellow-100 rounded-2xl p-5">
                <div class="font-semibold text-yellow-900">Catatan</div>
                <p class="text-yellow-800 text-sm mt-1">
                    Angka di atas adalah contoh untuk pratinjau. Saat terhubung dengan basis data,
                    angka akan mengikuti periode dan jenis laporan yang dipilih.
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const fmt = document.getElementById('lapFmt');
            const kind = document.getElementById('lapKind');
            const from = document.getElementById('lapFrom');
            const to   = document.getElementById('lapTo');
            const btnGen = document.getElementById('btnGen');
            const btnPdf = document.getElementById('btnPdf');
            const btnXls = document.getElementById('btnXls');
            const btnPrint = document.getElementById('btnPrint');

            btnGen.addEventListener('click', () => {
                // Contoh: rakit query untuk endpoint export kamu
                const q = new URLSearchParams({
                    from: from.value || '',
                    to: to.value || '',
                    kind: kind.value || 'ringkas',
                    format: fmt.value || 'pdf'
                }).toString();
                // Misal target route: /export?...
                const url = `/export?${q}`;
                if (fmt.value === 'pdf') btnPdf.href = url; else btnXls.href = url;
            });

            btnPrint.addEventListener('click', () => window.print());
        </script>
    @endpush
</x-layout>