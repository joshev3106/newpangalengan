<<<<<<< HEAD
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pangalengan - Pemetaan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 text-gray-900">
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto p-6">
        <!-- Content Header -->
        <div class="mb-6">
            <!-- Back Button -->
            <div class="mb-4">
                <button onclick="history.back()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali
                </button>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Peta & Puskesmas</h1>
            
            <!-- Tab Navigation -->
            <div class="flex space-x-2 mb-4">
            <button id="btn-stunting" 
                onclick="switchTab('stunting', event)" 
                class="flex-1 py-2 px-4 rounded-lg font-semibold text-gray-600 bg-gray-200 hover:bg-gray-300">
                Peta Stunting
            </button>
            <button id="btn-puskesmas" 
                onclick="switchTab('puskesmas', event)" 
                class="flex-1 py-2 px-4 rounded-lg font-semibold text-gray-600 bg-gray-200 hover:bg-gray-300">
                Puskesmas
            </button>
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
                <div class="bg-white p-6 rounded-xl shadow-md border-2 border-red-500 border-indigo-500">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Puskesmas Pangalengan</h3>
                    <p class="text-gray-600 leading-relaxed">Puskesmas utama yang melayani wilayah Pangalengan dengan fasilitas lengkap termasuk UGD 24 jam dan rawat inap.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md border-2 border-red-500 border-indigo-500">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Puskesmas Margamulya</h3>
                    <p class="text-gray-600 leading-relaxed">Puskesmas pembantu yang melayani daerah Margamulya dan sekitarnya dengan fokus pelayanan dasar.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md border-2 border-red-500 border-indigo-500">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script>
        // Global variables
        let stuntingMap, puskesmasMap;
        let mapsInitialized = false;

        // Sample data
        const stuntingData = [
            { name: "Margamulya", lat: -7.3167, lng: 107.5833, rate: 23.5, status: "high" },
            { name: "Warnasari", lat: -7.3267, lng: 107.5733, rate: 18.2, status: "medium" },
            { name: "Pangalengan", lat: -7.3067, lng: 107.5933, rate: 15.8, status: "medium" },
            { name: "Tribaktimulya", lat: -7.3367, lng: 107.5633, rate: 8.9, status: "low" },
            { name: "Pulosari", lat: -7.2967, lng: 107.6033, rate: 12.4, status: "medium" },
            { name: "Sukaluyu", lat: -7.3467, lng: 107.5533, rate: 6.7, status: "low" }
        ];

        const puskesmasData = [
            { name: "Puskesmas Pangalengan", lat: -7.3067, lng: 107.5933, type: "induk" },
            { name: "Puskesmas Margamulya", lat: -7.3167, lng: 107.5833, type: "pembantu" },
            { name: "Puskesmas Warnasari", lat: -7.3267, lng: 107.5733, type: "pembantu" },
            { name: "Posyandu Melati", lat: -7.3367, lng: 107.5633, type: "posyandu" },
            { name: "Posyandu Mawar", lat: -7.2967, lng: 107.6033, type: "posyandu" }
        ];

        // Initialize maps
        function initializeMaps() {
            console.log('Initializing stunting map...');
            if (mapsInitialized) return;

            try {
                // Check if map container exists
                const mapContainer = document.getElementById('map');
                if (!mapContainer) {
                    console.error('Map container not found');
                    return;
                }

                // Stunting Map
                stuntingMap = L.map('map').setView([-7.3167, 107.5833], 12);
                console.log('Map object created:', stuntingMap);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(stuntingMap);
                console.log('Tile layer added');

                // Add markers for stunting data
                stuntingData.forEach(item => {
                    const color = item.status === 'high' ? '#dc2626' : 
                                 item.status === 'medium' ? '#f97316' : '#16a34a';
                    
                    L.circleMarker([item.lat, item.lng], {
                        radius: 8,
                        fillColor: color,
                        color: '#fff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(stuntingMap)
                    .bindPopup(`<div class="p-2"><strong class="text-gray-800">${item.name}</strong><br><span class="text-gray-600">Tingkat Stunting: ${item.rate}%</span></div>`);
                });
                console.log('Markers added');

                mapsInitialized = true;
                console.log('Maps initialization completed');
                
            } catch (error) {
                console.error('Error initializing maps:', error);
            }
        }

        function initializePuskesmasMap() {
            if (document.getElementById('puskesmas-map') && !puskesmasMap) {
                puskesmasMap = L.map('puskesmas-map').setView([-7.3167, 107.5833], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(puskesmasMap);

                puskesmasData.forEach(item => {
                    const color = item.type === 'induk' ? '#3b82f6' : 
                                 item.type === 'pembantu' ? '#8b5cf6' : '#f59e0b';
                    
                    L.circleMarker([item.lat, item.lng], {
                        radius: 10,
                        fillColor: color,
                        color: '#fff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(puskesmasMap)
                    .bindPopup(`<div class="p-2"><strong class="text-gray-800">${item.name}</strong><br><span class="text-gray-600">Tipe: ${item.type.charAt(0).toUpperCase() + item.type.slice(1)}</span></div>`);
                });
            }
        }
        
        // Tab switching function
        function switchTab(tabName) {
            // Update tab buttons
            document.querySelectorAll("button").forEach(btn => {
                btn.classList.remove("bg-red-600","text-white","shadow");
                btn.classList.add("bg-gray-200","text-gray-600");
            });
            event.target.classList.remove("bg-gray-200","text-gray-600");
            event.target.classList.add("bg-red-600","text-white","shadow");

            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Show selected tab content
            document.getElementById(tabName + '-tab').classList.remove('hidden');

            // Initialize maps based on tab
            setTimeout(() => {
                if (tabName === 'stunting' && stuntingMap) {
                    stuntingMap.invalidateSize();
                } else if (tabName === 'puskesmas') {
                    initializePuskesmasMap();
                    setTimeout(() => puskesmasMap && puskesmasMap.invalidateSize(), 100);
                } 
            }, 100);
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeMaps();
        });

        // Make switchTab function available globally
        window.switchTab = switchTab;
    </script>
</body>
</html>
=======
<x-layout>
    @if (Auth::check() && Auth::user()->role === 'admin')
        <h1 class="h-[2000px]">Hai, ini peta Admin</h1>
    @else
        <h1 class="h-[2000px]">Hai, ini peta</h1>
    @endif
</x-layout>
>>>>>>> 5e72e353cbdc1c231dc14cc870b4f7596b7ae72b
