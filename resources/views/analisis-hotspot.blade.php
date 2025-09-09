<x-layout>
    @push('styles')
        {{-- Leaflet CSS --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
        <style>
            /* Ensure Leaflet map container has proper dimensions */
            .leaflet-container { height: 100%; width: 100%; }
            #hotspot-map { min-height: 400px; }
            @media (min-width: 768px) { #hotspot-map { min-height: 600px; } }
            
            /* Modal styles */
            .modal { @apply fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4; }
            .modal-content { @apply bg-white rounded-lg shadow-xl max-w-md w-full max-h-screen overflow-y-auto; }
            .hidden { display: none !important; }
            
            /* Form styles */
            .form-input { @apply w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500; }
            .form-label { @apply block text-sm font-medium text-gray-700 mb-1; }
            .form-select { @apply w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500; }
        </style>
    @endpush

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto p-6">
        <!-- Overview Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-red-500">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Analisis Spasial</h3>
                <p class="text-gray-600 leading-relaxed">Wilayah dengan konsentrasi tinggi kasus stunting berdasarkan analisis spasial dan clustering geografis menggunakan metode Getis-Ord Gi*.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-orange-500">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Hotspot Teridentifikasi</h3>
                <p class="text-gray-600 leading-relaxed">Ditemukan <span id="total-hotspots">6</span> cluster hotspot stunting yang memerlukan perhatian khusus dan intervensi intensif dari dinas kesehatan.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-yellow-500">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Metodologi</h3>
                <p class="text-gray-600 leading-relaxed">Menggunakan algoritma clustering spasial untuk mengidentifikasi area dengan pola distribusi stunting yang tidak acak.</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-red-600">
                <div class="text-3xl font-bold text-red-600 mb-1" id="high-confidence">3</div>
                <div class="text-sm text-gray-600">High Confidence (99%)</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-orange-600">
                <div class="text-3xl font-bold text-orange-600 mb-1" id="medium-confidence">2</div>
                <div class="text-sm text-gray-600">Medium Confidence (95%)</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-yellow-500">
                <div class="text-3xl font-bold text-yellow-600 mb-1" id="low-confidence">1</div>
                <div class="text-sm text-gray-600">Low Confidence (90%)</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-gray-400">
                <div class="text-3xl font-bold text-gray-600 mb-1" id="not-significant">0</div>
                <div class="text-sm text-gray-600">Not Significant</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mb-6">
            <div class="flex flex-wrap gap-4 justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">Manajemen Data Hotspot</h2>
                <div class="flex gap-2">
                    <button id="add-hotspot-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-md">
                        ➕ Tambah Hotspot
                    </button>
                    <button id="refresh-analysis-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                        🔄 Refresh Analisis
                    </button>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-xl shadow-md mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Data Hotspot Stunting</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Area</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Koordinat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Confidence</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="hotspot-table-body" class="bg-white divide-y divide-gray-200">
                        <!-- Table rows will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Hotspot Map -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
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
            <div id="hotspot-map" style="height: 600px; width: 100%;"></div>
        </div>

        <!-- Analysis Details -->
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Hasil Analisis</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Total Cluster Teridentifikasi:</span>
                        <span class="font-semibold text-gray-900" id="total-clusters">6 Area</span>
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

    <!-- Add/Edit Hotspot Modal -->
    <div id="hotspot-modal" class="modal hidden">
        <div class="modal-content">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="modal-title" class="text-lg font-semibold text-gray-900">Tambah Hotspot Baru</h3>
                    <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="hotspot-form">
                    <input type="hidden" id="hotspot-id" value="">
                    
                    <div class="mb-4">
                        <label for="hotspot-name" class="form-label">Nama Area *</label>
                        <input type="text" id="hotspot-name" name="name" class="form-input" required>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="hotspot-lat" class="form-label">Latitude *</label>
                            <input type="number" step="any" id="hotspot-lat" name="lat" class="form-input" required>
                        </div>
                        <div>
                            <label for="hotspot-lng" class="form-label">Longitude *</label>
                            <input type="number" step="any" id="hotspot-lng" name="lng" class="form-input" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="hotspot-confidence" class="form-label">Confidence Level</label>
                        <select id="hotspot-confidence" name="confidence" class="form-select">
                            <option value="0">Not Significant</option>
                            <option value="90">90% Confidence</option>
                            <option value="95">95% Confidence</option>
                            <option value="99">99% Confidence</option>
                        </select>
                    </div>
                    
                    <div class="mb-6">
                        <label for="hotspot-cases" class="form-label">Jumlah Kasus *</label>
                        <input type="number" id="hotspot-cases" name="cases" class="form-input" min="0" required>
                    </div>
                    
                    <div class="flex gap-3 justify-end">
                        <button type="button" id="cancel-btn" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal hidden">
        <div class="modal-content max-w-sm">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Hapus Hotspot</h3>
                    <p class="text-sm text-gray-500 mb-4">Apakah Anda yakin ingin menghapus hotspot ini? Tindakan ini tidak dapat dibatalkan.</p>
                    <div class="flex gap-3 justify-center">
                        <button id="cancel-delete" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                            Batal
                        </button>
                        <button id="confirm-delete" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Leaflet JS --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
        <script>
            // Global variables
            let hotspotMap;
            let hotspotData = [
                { id: 1, name: "Cluster 1 - Margamulya", lat: -7.3167, lng: 107.5833, confidence: 99, cases: 45 },
                { id: 2, name: "Cluster 2 - Warnasari", lat: -7.3267, lng: 107.5733, confidence: 95, cases: 32 },
                { id: 3, name: "Cluster 3 - Tribaktimulya", lat: -7.3367, lng: 107.5633, confidence: 90, cases: 28 },
                { id: 4, name: "Area Normal 1", lat: -7.3067, lng: 107.5933, confidence: 0, cases: 12 },
                { id: 5, name: "Area Normal 2", lat: -7.2967, lng: 107.6033, confidence: 0, cases: 8 },
                { id: 6, name: "Area Normal 3", lat: -7.3467, lng: 107.5533, confidence: 0, cases: 15 }
            ];
            let nextId = 7;
            let editingId = null;
            let deleteId = null;

            // Initialize map
            function initializeHotspotMap() {
                try {
                    const mapContainer = document.getElementById('hotspot-map');
                    if (!mapContainer) return;

                    hotspotMap = L.map('hotspot-map').setView([-7.3167, 107.5833], 12);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(hotspotMap);

                    updateMap();
                } catch (error) {
                    console.error('Error initializing hotspot map:', error);
                }
            }

            // Update map with current data
            function updateMap() {
                if (!hotspotMap) return;
                
                // Clear existing markers
                hotspotMap.eachLayer(layer => {
                    if (layer instanceof L.CircleMarker || layer instanceof L.Circle) {
                        hotspotMap.removeLayer(layer);
                    }
                });

                // Add markers for hotspot data
                hotspotData.forEach(item => {
                    const color = item.confidence === 99 ? '#dc2626' :
                                  item.confidence === 95 ? '#ea580c' :
                                  item.confidence === 90 ? '#facc15' : '#d1d5db';

                    const radius = item.confidence > 0 ? 15 : 8;
                    const opacity = item.confidence > 0 ? 0.8 : 0.5;

                    L.circleMarker([item.lat, item.lng], {
                        radius,
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

                // Add heat zones for high confidence areas
                hotspotData.filter(item => item.confidence >= 90).forEach(item => {
                    const radius = item.confidence === 99 ? 800 :
                                  item.confidence === 95 ? 600 : 400;
                    const color = item.confidence === 99 ? '#dc2626' :
                                  item.confidence === 95 ? '#ea580c' : '#facc15';

                    L.circle([item.lat, item.lng], {
                        radius,
                        fillColor: color,
                        color: color,
                        weight: 1,
                        opacity: 0.3,
                        fillOpacity: 0.1
                    }).addTo(hotspotMap);
                });
            }

            // Update statistics
            function updateStatistics() {
                const high = hotspotData.filter(item => item.confidence === 99).length;
                const medium = hotspotData.filter(item => item.confidence === 95).length;
                const low = hotspotData.filter(item => item.confidence === 90).length;
                const notSignificant = hotspotData.filter(item => item.confidence === 0).length;
                const total = hotspotData.length;

                document.getElementById('high-confidence').textContent = high;
                document.getElementById('medium-confidence').textContent = medium;
                document.getElementById('low-confidence').textContent = low;
                document.getElementById('not-significant').textContent = notSignificant;
                document.getElementById('total-hotspots').textContent = total;
                document.getElementById('total-clusters').textContent = `${total} Area`;
            }

            // Update table
            function updateTable() {
                const tbody = document.getElementById('hotspot-table-body');
                tbody.innerHTML = '';

                hotspotData.forEach(item => {
                    const confidenceBadge = item.confidence === 99 ? 
                        '<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">99% High</span>' :
                        item.confidence === 95 ? 
                        '<span class="px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 rounded-full">95% Medium</span>' :
                        item.confidence === 90 ? 
                        '<span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">90% Low</span>' :
                        '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Not Significant</span>';

                    const statusBadge = item.confidence > 0 ?
                        '<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Hotspot</span>' :
                        '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Normal</span>';

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.id}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.name}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.lat.toFixed(4)}, ${item.lng.toFixed(4)}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${confidenceBadge}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.cases}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${statusBadge}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="editHotspot(${item.id})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button onclick="deleteHotspot(${item.id})" class="text-red-600 hover:text-red-900">Hapus</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }

            // CRUD Operations
            function addHotspot() {
                editingId = null;
                document.getElementById('modal-title').textContent = 'Tambah Hotspot Baru';
                document.getElementById('hotspot-form').reset();
                document.getElementById('hotspot-id').value = '';
                document.getElementById('hotspot-modal').classList.remove('hidden');
            }

            function editHotspot(id) {
                const item = hotspotData.find(h => h.id === id);
                if (!item) return;

                editingId = id;
                document.getElementById('modal-title').textContent = 'Edit Hotspot';
                document.getElementById('hotspot-id').value = id;
                document.getElementById('hotspot-name').value = item.name;
                document.getElementById('hotspot-lat').value = item.lat;
                document.getElementById('hotspot-lng').value = item.lng;
                document.getElementById('hotspot-confidence').value = item.confidence;
                document.getElementById('hotspot-cases').value = item.cases;
                document.getElementById('hotspot-modal').classList.remove('hidden');
            }

            function deleteHotspot(id) {
                deleteId = id;
                document.getElementById('delete-modal').classList.remove('hidden');
            }

            function saveHotspot() {
                const formData = {
                    name: document.getElementById('hotspot-name').value.trim(),
                    lat: parseFloat(document.getElementById('hotspot-lat').value),
                    lng: parseFloat(document.getElementById('hotspot-lng').value),
                    confidence: parseInt(document.getElementById('hotspot-confidence').value),
                    cases: parseInt(document.getElementById('hotspot-cases').value)
                };

                // Validation
                if (!formData.name || isNaN(formData.lat) || isNaN(formData.lng) || isNaN(formData.cases)) {
                    alert('Harap isi semua field yang wajib diisi dengan benar!');
                    return;
                }

                if (editingId) {
                    // Update existing
                    const index = hotspotData.findIndex(h => h.id === editingId);
                    if (index !== -1) {
                        hotspotData[index] = { ...formData, id: editingId };
                    }
                } else {
                    // Add new
                    hotspotData.push({ ...formData, id: nextId++ });
                }

                closeModal();
                refreshView();
                alert(editingId ? 'Hotspot berhasil diupdate!' : 'Hotspot berhasil ditambahkan!');
            }

            function confirmDelete() {
                if (deleteId) {
                    hotspotData = hotspotData.filter(h => h.id !== deleteId);
                    closeDeleteModal();
                    refreshView();
                    alert('Hotspot berhasil dihapus!');
                }
            }

            function closeModal() {
                document.getElementById('hotspot-modal').classList.add('hidden');
                editingId = null;
            }

            function closeDeleteModal() {
                document.getElementById('delete-modal').classList.add('hidden');
                deleteId = null;
            }

            function refreshView() {
                updateMap();
                updateTable();
                updateStatistics();
            }

            function refreshAnalysis() {
                // Simulate analysis refresh
                alert('Analisis sedang diperbarui... Proses ini dapat memakan waktu beberapa menit.');
                // In real implementation, this would call backend API
            }

            // Event listeners
            document.addEventListener('DOMContentLoaded', () => {
                // Initialize map
                setTimeout(initializeHotspotMap, 300);
                
                // Initialize table and statistics
                updateTable();
                updateStatistics();

                // Modal event listeners
                document.getElementById('add-hotspot-btn').addEventListener('click', addHotspot);
                document.getElementById('refresh-analysis-btn').addEventListener('click', refreshAnalysis);
                
                // Form submission
                document.getElementById('hotspot-form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    saveHotspot();
                });

                // Modal close buttons
                document.getElementById('close-modal').addEventListener('click', closeModal);
                document.getElementById('cancel-btn').addEventListener('click', closeModal);
                
                // Delete modal buttons
                document.getElementById('cancel-delete').addEventListener('click', closeDeleteModal);
                document.getElementById('confirm-delete').addEventListener('click', confirmDelete);

                // Close modal when clicking outside
                document.getElementById('hotspot-modal').addEventListener('click', (e) => {
                    if (e.target === document.getElementById('hotspot-modal')) {
                        closeModal();
                    }
                });

                document.getElementById('delete-modal').addEventListener('click', (e) => {
                    if (e.target === document.getElementById('delete-modal')) {
                        closeDeleteModal();
                    }
                });

                // Keyboard shortcuts
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        closeModal();
                        closeDeleteModal();
                    }
                });
            });

            // Global functions for onclick handlers
            window.editHotspot = editHotspot;
            window.deleteHotspot = deleteHotspot;
        </script>
    @endpush
</x-layout>