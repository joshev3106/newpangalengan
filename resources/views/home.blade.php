<x-layout>
    @push('styles')
        {{-- Leaflet CSS untuk mini preview peta --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
        <style>
            .leaflet-container { height: 100%; width: 100%; }
            #home-mini-map { min-height: 280px; }
            @media (min-width: 768px) { #home-mini-map { min-height: 360px; } }
        </style>
    @endpush

    {{-- MAIN BODY --}}
    <div class="max-w-7xl mx-auto px-6 py-10">

        {{-- Pencarian cepat --}}
        <div class="mb-6">
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-4 md:p-5">
                <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                    <div class="relative flex-1">
                        <input id="search-desa" type="text" placeholder="Cari desa / puskesmas…"
                               class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500 pl-11">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
                        </svg>
                    </div>
                    <div class="flex gap-2 justify-evenly">
                        <button id="btn-filter-stunting"
                                class="px-4 py-2 rounded-xl bg-red-50 text-red-700 hover:bg-red-100 ring-1 ring-red-100">
                            Stunting
                        </button>
                        <a href="{{ route('peta') ?? '#' }}"
                           class="px-4 py-2 rounded-xl bg-red-700 text-white hover:bg-red-800">
                            Buka Peta
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dua kolom: Peta mini + Ringkasan --}}
        <div class="grid md:grid-cols-5 gap-6">
            {{-- Preview Peta (3 kolom) --}}
            <div class="md:col-span-3">
                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div class="font-semibold text-gray-800">Preview Peta</div>
                        <div class="flex items-center gap-4 text-sm">
                            <span class="inline-flex items-center gap-2">
                                <span class="w-3 h-3 rounded bg-red-600"></span><span>Hotspot</span>
                            </span>
                            <span class="inline-flex items-center gap-2">
                                <span class="w-3 h-3 rounded bg-blue-500"></span><span>Puskesmas</span>
                            </span>
                        </div>
                    </div>
                    <div id="home-mini-map" class="w-full"></div>
                </div>
            </div>

            {{-- Ringkasan (2 kolom) --}}
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-50 ring-1 ring-red-100 flex items-center justify-center">
                            <span class="text-red-600 text-lg">🔥</span>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">Hotspot Terkini</div>
                            <p class="text-gray-600 mt-1">
                                3 area dengan confidence 99% (Margamulya & sekitar), 2 area 95%.
                                Prioritaskan intervensi dan monitoring pekanan.
                            </p>
                            <div class="mt-3 flex gap-2">
                                <a href="{{ route('analisis-hotspot') ?? '#' }}"
                                   class="px-3 py-1.5 text-sm rounded-lg bg-red-600 text-white hover:bg-red-500">
                                    Lihat Analisis
                                </a>
                                <a href="{{ route('peta', ['tab' => 'stunting']) ?? '#' }}"
                                   class="px-3 py-1.5 text-sm rounded-lg bg-gray-100 hover:bg-gray-200">
                                    Pusatkan ke Hotspot
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 ring-1 ring-blue-100 flex items-center justify-center">
                            <span class="text-blue-600 text-lg">🏥</span>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">Jaringan Faskes</div>
                            <p class="text-gray-600 mt-1">
                                3 puskesmas induk, 4 pembantu, 5 posyandu aktif. Fokus KIA & gizi di area medium–tinggi.
                            </p>
                            <div class="mt-3 flex gap-2">
                                <a href="{{ route('peta', ['tab' => 'puskesmas']) ?? '#' }}"
                                   class="px-3 py-1.5 text-sm rounded-lg bg-blue-600 text-white hover:bg-blue-500">
                                    Lihat Peta Faskes
                                </a>
                                <a href="{{ route('laporan') ?? '#' }}"
                                   class="px-3 py-1.5 text-sm rounded-lg bg-gray-100 hover:bg-gray-200">
                                    Export Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>

        {{-- Section tautan cepat --}}
        <div class="mt-10 grid grid-cols-2 md:grid-cols-3 gap-6">
            <a href="{{ route('analisis-hotspot') ?? '#' }}"
               class="group bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6 hover:ring-red-200 hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 ring-1 ring-red-100 flex items-center justify-center">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="font-semibold text-gray-800 group-hover:text-red-700">Analisis Hotspot</div>
                </div>
                <p class="text-gray-600 mt-2 text-sm">Getis-Ord Gi*, cluster 90–99%, rekomendasi intervensi.</p>
            </a>

            <a href="{{ route('stunting.index') ?? '#' }}"
               class="group bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6 hover:ring-red-200 hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 ring-1 ring-red-100 flex items-center justify-center">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="font-semibold text-gray-800 group-hover:text-red-700">Data Stunting</div>
                </div>
                <p class="text-gray-600 mt-2 text-sm">Daftar ringkas kasus per desa. Filter & ekspor cepat.</p>
            </a>

            <a href="{{ route('peta', ['tab' => 'stunting']) ?? '#' }}"
               class="group bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6 hover:ring-red-200 hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 ring-1 ring-red-100 flex items-center justify-center">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                    <div class="font-semibold text-gray-800 group-hover:text-red-700">Peta Stunting</div>
                </div>
                <p class="text-gray-600 mt-2 text-sm">Per desa: tinggi, sedang, rendah. Popup detail & koordinat.</p>
            </a>

            <a href="{{ route('peta', ['tab' => 'puskesmas']) ?? '#' }}"
               class="group bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6 hover:ring-red-200 hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 ring-1 ring-red-100 flex items-center justify-center">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                    <div class="font-semibold text-gray-800 group-hover:text-red-700">Peta Puskesmas</div>
                </div>
                <p class="text-gray-600 mt-2 text-sm">Induk, pembantu, posyandu. Cakupan & prioritas layanan.</p>
            </a>

            <a href="{{ route('wilayah.index') ?? '#' }}"
               class="group bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6 hover:ring-red-200 hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 ring-1 ring-red-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="font-semibold text-gray-800 group-hover:text-red-700">Data Wilayah</div>
                </div>
                <p class="text-gray-600 mt-2 text-sm">Profil singkat desa: populasi, cakupan faskes, & tingkat stunting.</p>
            </a>

            <a href="{{ route('laporan') ?? '#' }}"
               class="group bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6 hover:ring-red-200 hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 ring-1 ring-red-100 flex items-center justify-center">
                    <!-- Dokumen (outline basic) -->
                    <svg class="w-4 h-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6M9 13h6M9 17h6"/>
                    </svg>
                    </div>
                    <div class="font-semibold text-gray-800 group-hover:text-red-700">Laporan</div>
                </div>
                <p class="text-gray-600 mt-2 text-sm">Generate ringkasan analisis dan ekspor ke PDF/Excel.</p>
            </a>
        </div>

        {{-- CTA Laporan --}}
        <div class="mt-10 bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <p class="text-gray-600">Generate ringkasan analisis & peta ke PDF/Excel.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('laporan') ?? '#' }}"
                   class="px-5 py-2.5 rounded-xl bg-gray-900 text-white hover:bg-gray-800">📄 Generate Laporan</a>
                <a href="{{ route('laporan') ?? '#' }}"
                   class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200">📊 Export Analisis</a>
            </div>
        </div>

        {{-- Info cepat / pengumuman --}}
    <div class="bg-gradient-to-r mt-5 from-yellow-50 to-amber-50 rounded-2xl ring-1 ring-amber-100 p-5">
            <div class="flex items-start gap-3">
                <span class="mt-0.5 text-amber-600">📢</span>
                <div>
                    <div class="font-semibold text-amber-800">Pengingat</div>
                    <p class="text-amber-700 text-sm mt-1">
                        Pembaruan data bulanan akan dilakukan pada tanggal 5 setiap bulan. Pastikan input posyandu telah lengkap.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
        <script>
            // Data contoh konsisten dengan halaman kamu sebelumnya
            const hotspotData = [
                { name: "Cluster 1 - Margamulya", lat: -7.3167, lng: 107.5833, confidence: 99 },
                { name: "Cluster 2 - Warnasari",  lat: -7.3267, lng: 107.5733, confidence: 95 },
                { name: "Tribaktimulya (90%)",    lat: -7.3367, lng: 107.5633, confidence: 90 },
            ];
            const faskesData = [
                { name: "Puskesmas Pangalengan", lat: -7.3067, lng: 107.5933, type: "induk" },
                { name: "Puskesmas Margamulya",  lat: -7.3167, lng: 107.5833, type: "pembantu" },
                { name: "Posyandu Melati",       lat: -7.3367, lng: 107.5633, type: "posyandu" },
            ];

            let miniMap, initialized = false;

            function initMiniMap() {
                if (initialized) return;
                const el = document.getElementById('home-mini-map');
                if (!el) return;

                // Inisialisasi peta
                miniMap = L.map('home-mini-map', { scrollWheelZoom: false }).setView([-7.3167, 107.5833], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(miniMap);

                // Hotspot markers
                hotspotData.forEach(h => {
                    const color = h.confidence === 99 ? '#dc2626' :
                                  h.confidence === 95 ? '#ea580c' : '#facc15';
                    L.circleMarker([h.lat, h.lng], {
                        radius: 9, fillColor: color, color: '#fff', weight: 2, opacity: 1, fillOpacity: 0.85
                    }).addTo(miniMap).bindPopup(`<strong>${h.name}</strong><br>Confidence: ${h.confidence}%`);
                });

                // Faskes markers
                faskesData.forEach(f => {
                    const color = f.type === 'induk' ? '#3b82f6' :
                                  f.type === 'pembantu' ? '#8b5cf6' : '#f59e0b';
                    L.circleMarker([f.lat, f.lng], {
                        radius: 8, fillColor: color, color: '#fff', weight: 2, opacity: 1, fillOpacity: 0.85
                    }).addTo(miniMap).bindPopup(`<strong>${f.name}</strong><br>Tipe: ${f.type}`);
                });

                initialized = true;
                // pastikan ukuran peta ok setelah transisi
                setTimeout(() => miniMap.invalidateSize(), 200);
            }

            // Lazy init ketika elemen terlihat di viewport
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => { if (entry.isIntersecting) initMiniMap(); });
            }, { threshold: 0.2 });
            observer.observe(document.getElementById('home-mini-map'));

            // Filter & pencarian dummy (frontend-only)
            const inputSearch = document.getElementById('search-desa');
            const btnSt = document.getElementById('btn-filter-stunting');
            const btnFk = document.getElementById('btn-filter-faskes');

            function highlightMarkers(type) {
                if (!initialized) initMiniMap();
                // Sederhana: zoom ke area & buka popup pertama
                if (type === 'stunting' && hotspotData.length) {
                    miniMap.setView([hotspotData[0].lat, hotspotData[0].lng], 13);
                } else if (type === 'faskes' && faskesData.length) {
                    miniMap.setView([faskesData[0].lat, faskesData[0].lng], 13);
                }
            }

            btnSt?.addEventListener('click', () => highlightMarkers('stunting'));
            btnFk?.addEventListener('click', () => highlightMarkers('faskes'));
            inputSearch?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    // contoh: cari by name lalu fokus
                    const q = e.target.value.trim().toLowerCase();
                    const found = hotspotData.find(d => d.name.toLowerCase().includes(q))
                               || faskesData.find(d => d.name.toLowerCase().includes(q));
                    if (found) {
                        initMiniMap();
                        miniMap.setView([found.lat, found.lng], 14);
                    }
                }
            });
        </script>
    @endpush
</x-layout>
