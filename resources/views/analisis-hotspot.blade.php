<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Hotspot</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Ensure Leaflet map container has proper dimensions */
        .leaflet-container {
            height: 100%;
            width: 100%;
        }
        
        /* Ensure map containers have minimum height */
        #hotspot-map {
            min-height: 400px;
        }
        
        @media (min-width: 768px) {
            #hotspot-map {
                min-height: 600px;
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    <!-- Back Navigation -->
    <div class="max-w-7xl mx-auto px-6 pt-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-4">
            <span class="text-gray-900 font-medium">Analisis Hotspot</span>
        </nav>
    </div>

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
            
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Analisis Hotspot Stunting</h1>
        </div>

        <!-- Overview Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-red-500">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Analisis Spasial</h3>
                <p class="text-gray-600 leading-relaxed">Wilayah dengan konsentrasi tinggi kasus stunting berdasarkan analisis spasial dan clustering geografis menggunakan metode Getis-Ord Gi*.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-orange-500">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Hotspot Teridentifikasi</h3>
                <p class="text-gray-600 leading-relaxed">Ditemukan 5 cluster hotspot stunting yang memerlukan perhatian khusus dan intervensi intensif dari dinas kesehatan.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-yellow-500">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Metodologi</h3>
                <p class="text-gray-600 leading-relaxed">Menggunakan algoritma clustering spasial untuk mengidentifikasi area dengan pola distribusi stunting yang tidak acak.</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-red-600">
                <div class="text-3xl font-bold text-red-600 mb-1">3</div>
                <div class="text-sm text-gray-600">High Confidence (99%)</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-orange-600">
                <div class="text-3xl font-bold text-orange-600 mb-1">2</div>
                <div class="text-sm text-gray-600">Medium Confidence (95%)</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-yellow-500">
                <div class="text-3xl font-bold text-yellow-600 mb-1">1</div>
                <div class="text-sm text-gray-600">Low Confidence (90%)</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-gray-400">
                <div class="text-3xl font-bold text-gray-600 mb-1">8</div>
                <div class="text-sm text-gray-600">Not Significant</div>
            </div>
        </div>

        <!-- Hotspot Map -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <!-- Map Header -->
            <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Peta Analisis Hotspot</h2>
                <div class="flex flex-wrap gap-6 items-center">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded bg-red-600 border-2 border-white shadow-sm"></div>
                        <span class="text-sm font-medium">Hot Spot (99% Confidence)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded bg-orange-600 border-2 border-white shadow-sm"></div>
                        <span class="text-sm font-medium">Hot Spot (95% Confidence)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded bg-yellow-400 border-2 border-white shadow-sm"></div>
                        <span class="text-sm font-medium">Hot Spot (90% Confidence)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded bg-gray-300 border-2 border-white shadow-sm"></div>
                        <span class="text-sm font-medium">Not Significant</span>
                    </div>
                </div>
            </div>
            <!-- Map Container -->
            <div id="hotspot-map" style="height: 600px; width: 100%;"></div>
        </div>

        <!-- Analysis Details -->
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Hasil Analisis</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Total Cluster Teridentifikasi:</span>
                        <span class="font-semibold text-gray-900">6 Area</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Tingkat Signifikansi:</span>
                        <span class="font-semibold text-gray-900">90-99%</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Metode Analisis:</span>
                        <span class="font-semibold text-gray-900">Getis-Ord Gi*</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Status:</span>
                        <span class="inline-block px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">Aktif</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Rekomendasi Tindakan</h3>
                <ul class="space-y-3 text-gray-600">
                    <li class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-red-500 rounded-full mt-2 flex-shrink-0"></div>
                        <span>Fokuskan program intervensi pada area dengan confidence 99%</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-orange-500 rounded-full mt-2 flex-shrink-0"></div>
                        <span>Tingkatkan monitoring pada cluster confidence 95%</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2 flex-shrink-0"></div>
                        <span>Lakukan pencegahan preventif pada area confidence 90%</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                        <span>Koordinasi dengan puskesmas setempat untuk program berkelanjutan</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Export/Action Buttons -->
        <div class="flex flex-wrap gap-4 justify-center">
            <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-md">
                📊 Export Analisis
            </button>
            <button class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-md">
                📋 Generate Laporan
            </button>
            <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                🗺️ Lihat Peta Wilayah
            </button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script>
        // Global variables
        let hotspotMap;

        // Sample hotspot data
        const hotspotData = [
            { name: "Cluster 1 - Margamulya", lat: -7.3167, lng: 107.5833, confidence: 99, cases: 45 },
            { name: "Cluster 2 - Warnasari", lat: -7.3267, lng: 107.5733, confidence: 95, cases: 32 },
            { name: "Cluster 3 - Tribaktimulya", lat: -7.3367, lng: 107.5633, confidence: 90, cases: 28 },
            { name: "Area Normal 1", lat: -7.3067, lng: 107.5933, confidence: 0, cases: 12 },
            { name: "Area Normal 2", lat: -7.2967, lng: 107.6033, confidence: 0, cases: 8 },
            { name: "Area Normal 3", lat: -7.3467, lng: 107.5533, confidence: 0, cases: 15 }
        ];

        function initializeHotspotMap() {
            console.log('Initializing hotspot map...');
            
            try {
                const mapContainer = document.getElementById('hotspot-map');
                if (!mapContainer) {
                    console.error('Hotspot map container not found');
                    return;
                }

                hotspotMap = L.map('hotspot-map').setView([-7.3167, 107.5833], 12);
                console.log('Hotspot map object created:', hotspotMap);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(hotspotMap);
                console.log('Hotspot tile layer added');

                // Add markers for hotspot data
                hotspotData.forEach(item => {
                    const color = item.confidence === 99 ? '#dc2626' :
                                 item.confidence === 95 ? '#ea580c' :
                                 item.confidence === 90 ? '#facc15' : '#d1d5db';
                    
                    const radius = item.confidence > 0 ? 15 : 8;
                    const opacity = item.confidence > 0 ? 0.8 : 0.5;
                    
                    L.circleMarker([item.lat, item.lng], {
                        radius: radius,
                        fillColor: color,
                        color: '#fff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: opacity
                    }).addTo(hotspotMap)
                    .bindPopup(`
                        <div class="p-3">
                            <strong class="text-gray-800 text-base">${item.name}</strong><br>
                            <span class="text-gray-600">
                                ${item.confidence > 0 ? `Confidence: ${item.confidence}%` : 'Not Significant'}<br>
                                Kasus: ${item.cases} anak
                            </span>
                        </div>
                    `);
                });
                console.log('Hotspot markers added');

                // Add heat zones for high confidence areas
                hotspotData.filter(item => item.confidence >= 90).forEach(item => {
                    const radius = item.confidence === 99 ? 800 : 
                                  item.confidence === 95 ? 600 : 400;
                    const color = item.confidence === 99 ? '#dc2626' :
                                 item.confidence === 95 ? '#ea580c' : '#facc15';
                    
                    L.circle([item.lat, item.lng], {
                        radius: radius,
                        fillColor: color,
                        color: color,
                        weight: 1,
                        opacity: 0.3,
                        fillOpacity: 0.1
                    }).addTo(hotspotMap);
                });
                
                console.log('Hotspot map initialization completed');
                
            } catch (error) {
                console.error('Error initializing hotspot map:', error);
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing hotspot map...');
            
            setTimeout(() => {
                initializeHotspotMap();
            }, 500);
        });
    </script>
</body>
</html>