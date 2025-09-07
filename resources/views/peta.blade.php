<x-layout>
    @push('styles')
        {{-- Leaflet CSS --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
    @endpush

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto p-6">
        <!-- Content Header -->
        <div class="mb-6">
            <!-- Tab Navigation -->
            <div id="tab-nav" class="flex space-x-2 mb-4">
                <button id="btn-stunting"
                    onclick="switchTab('stunting', event)"
                    class="flex-1 py-2 px-4 rounded-lg font-semibold bg-red-600 text-white shadow">
                    Peta Stunting
                </button>
                <button id="btn-puskesmas"
                    onclick="switchTab('puskesmas', event)"
                    class="flex-1 py-2 px-4 rounded-lg font-semibold text-gray-600 bg-gray-200 hover:bg-gray-300 hover:cursor-pointer">
                    Puskesmas
                </button>
            </div>
        </div>

        <!-- Peta Stunting Tab -->
        <div id="stunting-tab" class="tab-content">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <!-- Map Header -->
                <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <div class="flex flex-wrap gap-6 items-center">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-red-500 border-2 border-white shadow-sm"></div>
                            <span class="text-sm font-medium">Tinggi (&gt;20%)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-orange-500 border-2 border-white shadow-sm"></div>
                            <span class="text-sm font-medium">Sedang (10-20%)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-green-500 border-2 border-white shadow-sm"></div>
                            <span class="text-sm font-medium">Rendah (&lt;10%)</span>
                        </div>
                    </div>
                </div>
                <!-- Map Container -->
                <div id="map" class="h-96 md:h-[600px] w-full"></div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-white p-6 rounded-xl shadow-md text-center">
                    <div class="text-3xl font-bold text-gray-900 mb-1">12</div>
                    <div class="text-sm text-gray-600">Desa Tinggi</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md text-center">
                    <div class="text-3xl font-bold text-gray-900 mb-1">8</div>
                    <div class="text-sm text-gray-600">Desa Sedang</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md text-center">
                    <div class="text-3xl font-bold text-gray-900 mb-1">15</div>
                    <div class="text-sm text-gray-600">Desa Rendah</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md text-center">
                    <div class="text-3xl font-bold text-gray-900 mb-1">18.5%</div>
                    <div class="text-sm text-gray-600">Rata-rata Stunting</div>
                </div>
            </div>
        </div>

        <!-- Puskesmas Tab -->
        <div id="puskesmas-tab" class="tab-content hidden">
            <!-- Info Cards -->
            <div class="grid md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-6 rounded-xl shadow-md border-2 border-indigo-500">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Puskesmas Pangalengan</h3>
                    <p class="text-gray-600 leading-relaxed">Puskesmas utama yang melayani wilayah Pangalengan dengan fasilitas lengkap termasuk UGD 24 jam dan rawat inap.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md border-2 border-indigo-500">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Puskesmas Margamulya</h3>
                    <p class="text-gray-600 leading-relaxed">Puskesmas pembantu yang melayani daerah Margamulya dan sekitarnya dengan fokus pelayanan dasar.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md border-2 border-indigo-500">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Puskesmas Warnasari</h3>
                    <p class="text-gray-600 leading-relaxed">Puskesmas dengan layanan khusus kesehatan ibu dan anak serta program pencegahan stunting.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <!-- Map Header -->
                <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <div class="flex flex-wrap gap-6 items-center">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded-full bg-blue-500 border-2 border-white shadow-sm"></div>
                            <span class="text-sm font-medium">Puskesmas Induk</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded-full bg-purple-500 border-2 border-white shadow-sm"></div>
                            <span class="text-sm font-medium">Puskesmas Pembantu</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded-full bg-amber-500 border-2 border-white shadow-sm"></div>
                            <span class="text-sm font-medium">Posyandu</span>
                        </div>
                    </div>
                </div>
                <!-- Map Container -->
                <div id="puskesmas-map" class="h-96 md:h-[600px] w-full"></div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Leaflet JS --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
        <script>
            // Global variables
            let stuntingMap, puskesmasMap;
            let mapsInitialized = false;

            // Sample data
            const stuntingData = [
                { name: "Margamulya",   lat: -7.3167, lng: 107.5833, rate: 23.5, status: "high"   },
                { name: "Warnasari",    lat: -7.3267, lng: 107.5733, rate: 18.2, status: "medium" },
                { name: "Pangalengan",  lat: -7.3067, lng: 107.5933, rate: 15.8, status: "medium" },
                { name: "Tribaktimulya",lat: -7.3367, lng: 107.5633, rate: 8.9,  status: "low"    },
                { name: "Pulosari",     lat: -7.2967, lng: 107.6033, rate: 12.4, status: "medium" },
                { name: "Sukaluyu",     lat: -7.3467, lng: 107.5533, rate: 6.7,  status: "low"    }
            ];

            const puskesmasData = [
                { name: "Puskesmas Pangalengan", lat: -7.3067, lng: 107.5933, type: "induk"    },
                { name: "Puskesmas Margamulya",  lat: -7.3167, lng: 107.5833, type: "pembantu" },
                { name: "Puskesmas Warnasari",   lat: -7.3267, lng: 107.5733, type: "pembantu" },
                { name: "Posyandu Melati",       lat: -7.3367, lng: 107.5633, type: "posyandu" },
                { name: "Posyandu Mawar",        lat: -7.2967, lng: 107.6033, type: "posyandu" }
            ];

            // Initialize maps
            function initializeMaps() {
                if (mapsInitialized) return;

                // Stunting Map
                const mapEl = document.getElementById('map');
                if (!mapEl) return;

                stuntingMap = L.map('map').setView([-7.3167, 107.5833], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(stuntingMap);

                // Add markers for stunting data
                stuntingData.forEach(item => {
                    const color = item.status === 'high' ? '#dc2626'
                                : item.status === 'medium' ? '#f97316'
                                : '#16a34a';

                    L.circleMarker([item.lat, item.lng], {
                        radius: 8,
                        fillColor: color,
                        color: '#fff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(stuntingMap)
                      .bindPopup(
                        `<div class="p-2">
                            <strong class="text-gray-800">${item.name}</strong><br>
                            <span class="text-gray-600">Tingkat Stunting: ${item.rate}%</span>
                         </div>`
                      );
                });

                mapsInitialized = true;
            }

            function initializePuskesmasMap() {
                if (!document.getElementById('puskesmas-map') || puskesmasMap) return;

                puskesmasMap = L.map('puskesmas-map').setView([-7.3167, 107.5833], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(puskesmasMap);

                puskesmasData.forEach(item => {
                    const color = item.type === 'induk' ? '#3b82f6'
                                : item.type === 'pembantu' ? '#8b5cf6'
                                : '#f59e0b';

                    L.circleMarker([item.lat, item.lng], {
                        radius: 10,
                        fillColor: color,
                        color: '#fff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(puskesmasMap)
                      .bindPopup(
                        `<div class="p-2">
                            <strong class="text-gray-800">${item.name}</strong><br>
                            <span class="text-gray-600">Tipe: ${item.type.charAt(0).toUpperCase() + item.type.slice(1)}</span>
                         </div>`
                      );
                });
            }

            // Tab switching (scoped to nav)
            function switchTab(tabName, evt) {
                // Update tab buttons only inside #tab-nav
                const nav = document.getElementById('tab-nav');
                nav.querySelectorAll('button').forEach(btn => {
                    btn.classList.remove('bg-red-600','text-white','shadow');
                    btn.classList.add('bg-gray-200','text-gray-600');
                });
                if (evt && evt.target) {
                    evt.target.classList.remove('bg-gray-200','text-gray-600');
                    evt.target.classList.add('bg-red-600','text-white','shadow');
                }

                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));

                // Show selected tab
                const target = document.getElementById(`${tabName}-tab`);
                target.classList.remove('hidden');

                // Initialize / refresh maps
                setTimeout(() => {
                    if (tabName === 'stunting') {
                        if (!mapsInitialized) initializeMaps();
                        if (stuntingMap) stuntingMap.invalidateSize();
                    } else if (tabName === 'puskesmas') {
                        initializePuskesmasMap();
                        setTimeout(() => puskesmasMap && puskesmasMap.invalidateSize(), 100);
                    }
                }, 100);
            }

            // Initialize when page loads (default: stunting tab)
            document.addEventListener('DOMContentLoaded', () => {
                initializeMaps();
            });

            // Make available globally
            window.switchTab = switchTab;
        </script>
    @endpush
</x-layout>
