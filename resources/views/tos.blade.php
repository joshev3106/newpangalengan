<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kecamatan Pangalengan - Monitoring Stunting</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo h1 {
            color: #2d3748;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .nav-menu {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-menu a {
            text-decoration: none;
            color: #4a5568;
            font-weight: 500;
            transition: color 0.3s;
            cursor: pointer;
        }

        .nav-menu a:hover, .nav-menu a.active {
            color: #667eea;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 6rem 2rem 2rem;
        }

        .hero {
            text-align: center;
            margin-bottom: 3rem;
            color: white;
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #4a5568;
            font-weight: 500;
        }

        .section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            margin-bottom: 2rem;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            display: none;
        }

        .section.active {
            display: block;
        }

        .section h2 {
            color: #2d3748;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }

        .section-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .tab-button {
            padding: 0.75rem 1.5rem;
            border: none;
            background: #e2e8f0;
            color: #4a5568;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }

        .tab-button.active {
            background: #667eea;
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .chart-container {
            position: relative;
            height: 400px;
            margin-bottom: 2rem;
        }

        .map-container {
            height: 500px;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            background: #f7fafc;
            margin-bottom: 2rem;
            transition: all 0.3s;
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: #667eea;
            background: #edf2f7;
        }

        .upload-area input[type="file"] {
            display: none;
        }

        .upload-button {
            background: #667eea;
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s;
        }

        .upload-button:hover {
            background: #5a67d8;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .data-table th {
            background: #f7fafc;
            color: #2d3748;
            font-weight: 600;
        }

        .severity-high { color: #e53e3e; font-weight: 600; }
        .severity-medium { color: #dd6b20; font-weight: 600; }
        .severity-low { color: #38a169; font-weight: 600; }

        .hotspot-legend {
            display: flex;
            gap: 2rem;
            margin: 1rem 0;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }

        .infrastructure-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .infrastructure-card {
            background: #f7fafc;
            padding: 1.5rem;
            border-radius: 15px;
            border-left: 4px solid #667eea;
        }

        .infrastructure-card h3 {
            color: #2d3748;
            margin-bottom: 1rem;
        }

        .infrastructure-list {
            list-style: none;
        }

        .infrastructure-list li {
            padding: 0.5rem 0;
            color: #4a5568;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-active {
            background: #c6f6d5;
            color: #22543d;
        }

        .status-inactive {
            background: #fed7d7;
            color: #742a2a;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .alert-error {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #feb2b2;
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #2d3748;
            cursor: pointer;
        }

        .overview-section {
            display: block;
        }

        @media (max-width: 768px) {
            .nav-menu {
                display: none;
                position: fixed;
                top: 100%;
                left: 0;
                width: 100%;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                flex-direction: column;
                padding: 2rem;
                box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            }

            .nav-menu.active {
                display: flex;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .section-tabs {
                flex-wrap: wrap;
            }

            .hotspot-legend {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <h1>Dashboard Pangalengan</h1>
            </div>
            <ul class="nav-menu" id="nav-menu">
                <li><a onclick="showSection('overview')" class="active">Overview</a></li>
                <li><a onclick="showSection('data-upload')">Upload Data</a></li>
                <li><a onclick="showSection('mapping')">Pemetaan</a></li>
                <li><a onclick="showSection('analysis')">Analisis</a></li>
                <li><a onclick="showSection('infrastructure')">Infrastruktur</a></li>
            </ul>
            <button class="mobile-menu-toggle" id="mobile-toggle" onclick="toggleMobileMenu()">☰</button>
        </div>
    </nav>

    <div class="container">
        <!-- Hero Section -->
        <div class="hero">
            <h1>Dashboard Monitoring Stunting</h1>
            <p>Sistem Informasi Terintegrasi untuk Monitoring dan Analisis Data Stunting di Kecamatan Pangalengan, Kabupaten Bandung</p>
        </div>

        <!-- Statistics Overview -->
        <div class="stats-grid overview-section">
            <div class="stat-card">
                <div class="stat-number" id="total-children">1,245</div>
                <div class="stat-label">Total Balita Terpantau</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="stunting-cases">187</div>
                <div class="stat-label">Kasus Stunting</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="stunting-percentage">15.0%</div>
                <div class="stat-label">Prevalensi Stunting</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="puskesmas-count">8</div>
                <div class="stat-label">Puskesmas Aktif</div>
            </div>
        </div>

        <!-- Data Upload Section -->
        <div class="section" id="data-upload-section">
            <h2>Upload Data Stunting</h2>
            
            <div id="upload-alerts"></div>
            
            <div class="upload-area" onclick="document.getElementById('file-input').click()">
                <input type="file" id="file-input" accept=".csv,.xlsx,.xls" multiple>
                <h3>Klik untuk Upload File</h3>
                <p>Mendukung format CSV, Excel (.xlsx, .xls)</p>
                <button type="button" class="upload-button">Pilih File</button>
            </div>
            
            <div id="file-list"></div>
            <div id="data-preview"></div>
        </div>

        <!-- Mapping Section -->
        <div class="section" id="mapping-section">
            <h2>Pemetaan Wilayah & Fasilitas Kesehatan</h2>
            <div class="section-tabs">
                <button class="tab-button active" onclick="switchTab('stunting-map')">Peta Stunting</button>
                <button class="tab-button" onclick="switchTab('puskesmas-map')">Puskesmas</button>
                <button class="tab-button" onclick="switchTab('hotspot-map')">Hotspot Analysis</button>
            </div>

            <div class="tab-content active" id="stunting-map">
                <div class="hotspot-legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background: #e53e3e;"></div>
                        <span>Tinggi (>20%)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #dd6b20;"></div>
                        <span>Sedang (10-20%)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #38a169;"></div>
                        <span>Rendah (<10%)</span>
                    </div>
                </div>
                <div class="map-container" id="stunting-map-container"></div>
            </div>

            <div class="tab-content" id="puskesmas-map">
                <div class="map-container" id="puskesmas-map-container"></div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Puskesmas</th>
                            <th>Desa</th>
                            <th>Status</th>
                            <th>Balita Terpantau</th>
                            <th>Kasus Stunting</th>
                        </tr>
                    </thead>
                    <tbody id="puskesmas-table"></tbody>
                </table>
            </div>

            <div class="tab-content" id="hotspot-map">
                <div class="map-container" id="hotspot-map-container"></div>
                <p><strong>Analisis Hotspot:</strong> Wilayah dengan konsentrasi tinggi kasus stunting berdasarkan analisis spasial dan clustering geografis.</p>
            </div>
        </div>

        <!-- Analysis Section -->
        <div class="section" id="analysis-section">
            <h2>Analisis Data Stunting</h2>
            <div class="section-tabs">
                <button class="tab-button active" onclick="switchTab('trend-chart')">Tren Bulanan</button>
                <button class="tab-button" onclick="switchTab('village-chart')">Per Desa</button>
                <button class="tab-button" onclick="switchTab('age-chart')">Kelompok Umur</button>
                <button class="tab-button" onclick="switchTab('gender-chart')">Jenis Kelamin</button>
            </div>

            <div class="tab-content active" id="trend-chart">
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <div class="tab-content" id="village-chart">
                <div class="chart-container">
                    <canvas id="villageChart"></canvas>
                </div>
            </div>

            <div class="tab-content" id="age-chart">
                <div class="chart-container">
                    <canvas id="ageChart"></canvas>
                </div>
            </div>

            <div class="tab-content" id="gender-chart">
                <div class="chart-container">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Infrastructure Section -->
        <div class="section" id="infrastructure-section">
            <h2>Infrastruktur Kesehatan Kecamatan Pangalengan</h2>
            <div class="infrastructure-grid">
                <div class="infrastructure-card">
                    <h3>Fasilitas Kesehatan</h3>
                    <ul class="infrastructure-list">
                        <li>Puskesmas <span class="status-badge status-active">8 Aktif</span></li>
                        <li>Pustu <span class="status-badge status-active">15 Aktif</span></li>
                        <li>Poskesdes <span class="status-badge status-active">23 Aktif</span></li>
                        <li>Posyandu <span class="status-badge status-active">85 Aktif</span></li>
                    </ul>
                </div>
                <div class="infrastructure-card">
                    <h3>Tenaga Kesehatan</h3>
                    <ul class="infrastructure-list">
                        <li>Dokter <span class="status-badge status-active">12 Orang</span></li>
                        <li>Bidan <span class="status-badge status-active">28 Orang</span></li>
                        <li>Perawat <span class="status-badge status-active">35 Orang</span></li>
                        <li>Ahli Gizi <span class="status-badge status-active">8 Orang</span></li>
                    </ul>
                </div>
                <div class="infrastructure-card">
                    <h3>Program Kesehatan</h3>
                    <ul class="infrastructure-list">
                        <li>Pemantauan Gizi <span class="status-badge status-active">Berjalan</span></li>
                        <li>Imunisasi <span class="status-badge status-active">Berjalan</span></li>
                        <li>Pemberian PMT <span class="status-badge status-active">Berjalan</span></li>
                        <li>Edukasi Gizi <span class="status-badge status-active">Berjalan</span></li>
                    </ul>
                </div>
                <div class="infrastructure-card">
                    <h3>Cakupan Wilayah</h3>
                    <ul class="infrastructure-list">
                        <li>Desa Terlayani <span class="status-badge status-active">18 Desa</span></li>
                        <li>Luas Wilayah <span>195.49 km²</span></li>
                        <li>Jumlah Penduduk <span>89,432 Jiwa</span></li>
                        <li>Kepadatan <span>458/km²</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sample data for demonstration
        const stuntingData = {
            villages: [
                { name: 'Pangalengan', stunting: 18.5, total: 89, lat: -7.1234, lng: 107.5876 },
                { name: 'Warnasari', stunting: 12.3, total: 73, lat: -7.1456, lng: 107.6123 },
                { name: 'Tribaktimulya', stunting: 22.1, total: 95, lat: -7.1678, lng: 107.5654 },
                { name: 'Margamulya', stunting: 15.7, total: 76, lat: -7.1345, lng: 107.6234 },
                { name: 'Sukarame', stunting: 19.8, total: 81, lat: -7.1567, lng: 107.5987 },
                { name: 'Lamajang', stunting: 8.9, total: 67, lat: -7.1234, lng: 107.6345 },
                { name: 'Margamekar', stunting: 25.4, total: 118, lat: -7.1789, lng: 107.5432 },
                { name: 'Banjarsari', stunting: 14.2, total: 71, lat: -7.1456, lng: 107.6567 }
            ],
            puskesmas: [
                { name: 'Puskesmas Pangalengan', village: 'Pangalengan', lat: -7.1234, lng: 107.5876, status: 'Aktif', patients: 245 },
                { name: 'Puskesmas Warnasari', village: 'Warnasari', lat: -7.1456, lng: 107.6123, status: 'Aktif', patients: 189 },
                { name: 'Puskesmas Tribaktimulya', village: 'Tribaktimulya', lat: -7.1678, lng: 107.5654, status: 'Aktif', patients: 203 },
                { name: 'Puskesmas Margamulya', village: 'Margamulya', lat: -7.1345, lng: 107.6234, status: 'Aktif', patients: 167 },
                { name: 'Puskesmas Sukarame', village: 'Sukarame', lat: -7.1567, lng: 107.5987, status: 'Aktif', patients: 178 },
                { name: 'Puskesmas Lamajang', village: 'Lamajang', lat: -7.1234, lng: 107.6345, status: 'Aktif', patients: 134 },
                { name: 'Puskesmas Margamekar', village: 'Margamekar', lat: -7.1789, lng: 107.5432, status: 'Aktif', patients: 221 },
                { name: 'Puskesmas Banjarsari', village: 'Banjarsari', lat: -7.1456, lng: 107.6567, status: 'Aktif', patients: 108 }
            ]
        };

        // Global variables for maps and charts
        let stuntingMap, puskesmasMap, hotspotMap;
        let trendChart, villageChart, ageChart, genderChart;
        let mapsInitialized = false;
        let chartsInitialized = false;

        // Main section switching function
        function showSection(sectionName) {
            // Hide all sections
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show/hide overview elements
            const overviewElements = document.querySelectorAll('.overview-section');
            if (sectionName === 'overview') {
                overviewElements.forEach(el => el.style.display = 'grid');
            } else {
                overviewElements.forEach(el => el.style.display = 'none');
            }
            
            // Show selected section
            if (sectionName !== 'overview') {
                const targetSection = document.getElementById(sectionName + '-section');
                if (targetSection) {
                    targetSection.classList.add('active');
                }
            }
            
            // Update navigation active state
            document.querySelectorAll('.nav-menu a').forEach(link => {
                link.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Initialize maps and charts when needed
            setTimeout(() => {
                if (sectionName === 'mapping' && !mapsInitialized) {
                    initializeMaps();
                    mapsInitialized = true;
                }
                
                if (sectionName === 'analysis' && !chartsInitialized) {
                    initializeCharts();
                    chartsInitialized = true;
                }
                
                // Refresh maps if they exist
                if (sectionName === 'mapping') {
                    refreshMaps();
                }
            }, 100);
        }

        // Tab switching function within sections
        function switchTab(tabName) {
            // Get the parent section
            const parentSection = event.target.closest('.section');
            
            // Hide all tab contents in this section
            parentSection.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tab buttons in this section
            parentSection.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked button
            event.target.classList.add('active');
            
            // Refresh maps if needed
            setTimeout(() => {
                if (tabName.includes('map')) {
                    refreshMaps();
                }
            }, 100);
        }

        // Mobile menu toggle
        function toggleMobileMenu() {
            const navMenu = document.getElementById('nav-menu');
            navMenu.classList.toggle('active');
        }

        // File upload handling
        document.getElementById('file-input').addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            const fileList = document.getElementById('file-list');
            const alertsContainer = document.getElementById('upload-alerts');
            
            if (files.length === 0) return;
            
            // Clear previous alerts
            alertsContainer.innerHTML = '';
            
            // Show file list
            fileList.innerHTML = `
                <h4>File yang dipilih:</h4>
                <ul>
                    ${files.map(file => `<li>${file.name} (${(file.size/1024/1024).toFixed(2)} MB)</li>`).join('')}
                </ul>
                <p style="margin-top: 1rem; color: #4a5568;">Simulasi upload berhasil! Dalam aplikasi asli, file akan diproses ke server.</p>
            `;
            
            // Simulate successful upload
            setTimeout(() => {
                alertsContainer.innerHTML = `
                    <div class="alert alert-success">
                        ${files.length} file berhasil diupload dan diproses!
                    </div>
                `;
                
                // Simulate data update
                updateStats();
            }, 1500);
        });

        // Update statistics simulation
        function updateStats() {
            const totalElement = document.getElementById('total-children');
            const stuntingElement = document.getElementById('stunting-cases');
            const percentageElement = document.getElementById('stunting-percentage');
            
            // Simulate new data
            const newTotal = parseInt(totalElement.textContent.replace(',', '')) + Math.floor(Math.random() * 50);
            const newStunting = parseInt(stuntingElement.textContent) + Math.floor(Math.random() * 10);
            const newPercentage = ((newStunting / newTotal) * 100).toFixed(1);
            
            totalElement.textContent = newTotal.toLocaleString();
            stuntingElement.textContent = newStunting;
            percentageElement.textContent = newPercentage + '%';
        }

        // Initialize maps
        function initializeMaps() {
            // Stunting Distribution Map
            stuntingMap = L.map('stunting-map-container').setView([-7.145, 107.6], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(stuntingMap);

            stuntingData.villages.forEach(village => {
                const color = village.stunting > 20 ? '#e53e3e' : village.stunting > 10 ? '#dd6b20' : '#38a169';
                L.circleMarker([village.lat, village.lng], {
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.7,
                    radius: Math.max(village.stunting / 2, 5)
                }).addTo(stuntingMap)
                .bindPopup(`
                    <strong>${village.name}</strong><br>
                    Prevalensi Stunting: ${village.stunting}%<br>
                    Total Balita: ${village.total}
                `);
            });

            // Puskesmas Map
            puskesmasMap = L.map('puskesmas-map-container').setView([-7.145, 107.6], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(puskesmasMap);

            stuntingData.puskesmas.forEach(puskesmas => {
                L.marker([puskesmas.lat, puskesmas.lng])
                .addTo(puskesmasMap)
                .bindPopup(`
                    <strong>${puskesmas.name}</strong><br>
                    Desa: ${puskesmas.village}<br>
                    Status: ${puskesmas.status}<br>
                    Balita Terpantau: ${puskesmas.patients}
                `);
            });

            // Hotspot Map
            hotspotMap = L.map('hotspot-map-container').setView([-7.145, 107.6], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(hotspotMap);

            // Add hotspot clusters
            const hotspots = stuntingData.villages.filter(v => v.stunting > 18);
            hotspots.forEach(hotspot => {
                L.circle([hotspot.lat, hotspot.lng], {
                    color: '#e53e3e',
                    fillColor: '#e53e3e',
                    fillOpacity: 0.3,
                    radius: 500
                }).addTo(hotspotMap)
                .bindPopup(`<strong>Hotspot: ${hotspot.name}</strong><br>Prevalensi: ${hotspot.stunting}%`);
            });

            // Populate Puskesmas table
            populatePuskesmasTable();
        }

        // Refresh maps function
        function refreshMaps() {
            setTimeout(() => {
                if (stuntingMap) stuntingMap.invalidateSize();
                if (puskesmasMap) puskesmasMap.invalidateSize();
                if (hotspotMap) hotspotMap.invalidateSize();
            }, 100);
        }

        // Populate Puskesmas table
        function populatePuskesmasTable() {
            const tableBody = document.getElementById('puskesmas-table');
            tableBody.innerHTML = '';
            
            stuntingData.puskesmas.forEach(puskesmas => {
                const stuntingCases = Math.floor(puskesmas.patients * 0.15); // 15% simulation
                const severity = stuntingCases > 30 ? 'severity-high' : stuntingCases > 15 ? 'severity-medium' : 'severity-low';
                
                const row = `
                    <tr>
                        <td>${puskesmas.name}</td>
                        <td>${puskesmas.village}</td>
                        <td><span class="status-badge status-active">${puskesmas.status}</span></td>
                        <td>${puskesmas.patients}</td>
                        <td><span class="${severity}">${stuntingCases}</span></td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        }

        // Initialize charts
        function initializeCharts() {
            // Trend Chart
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            trendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
                    datasets: [{
                        label: 'Kasus Stunting',
                        data: [165, 172, 158, 183, 177, 192, 187, 179],
                        borderColor: '#e53e3e',
                        backgroundColor: 'rgba(229, 62, 62, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Target Penurunan',
                        data: [165, 160, 155, 150, 145, 140, 135, 130],
                        borderColor: '#38a169',
                        backgroundColor: 'rgba(56, 161, 105, 0.1)',
                        borderDash: [5, 5],
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Tren Kasus Stunting Bulanan',
                            font: {
                                size: 16
                            }
                        },
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'Jumlah Kasus'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Bulan'
                            }
                        }
                    }
                }
            });

            // Village Chart
            const villageCtx = document.getElementById('villageChart').getContext('2d');
            villageChart = new Chart(villageCtx, {
                type: 'bar',
                data: {
                    labels: stuntingData.villages.map(v => v.name),
                    datasets: [{
                        label: 'Prevalensi Stunting (%)',
                        data: stuntingData.villages.map(v => v.stunting),
                        backgroundColor: stuntingData.villages.map(v => 
                            v.stunting > 20 ? '#e53e3e' : v.stunting > 10 ? '#dd6b20' : '#38a169'
                        ),
                        borderColor: stuntingData.villages.map(v => 
                            v.stunting > 20 ? '#c53030' : v.stunting > 10 ? '#c05621' : '#2f855a'
                        ),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Prevalensi Stunting per Desa',
                            font: {
                                size: 16
                            }
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Prevalensi (%)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Desa'
                            }
                        }
                    }
                }
            });

            // Age Chart
            const ageCtx = document.getElementById('ageChart').getContext('2d');
            ageChart = new Chart(ageCtx, {
                type: 'doughnut',
                data: {
                    labels: ['0-6 bulan', '7-12 bulan', '13-24 bulan', '25-36 bulan', '37-60 bulan'],
                    datasets: [{
                        data: [15, 28, 45, 52, 47],
                        backgroundColor: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Distribusi Stunting berdasarkan Kelompok Umur',
                            font: {
                                size: 16
                            }
                        },
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });

            // Gender Chart
            const genderCtx = document.getElementById('genderChart').getContext('2d');
            genderChart = new Chart(genderCtx, {
                type: 'bar',
                data: {
                    labels: ['Laki-laki', 'Perempuan'],
                    datasets: [{
                        label: 'Kasus Stunting',
                        data: [102, 85],
                        backgroundColor: ['#667eea', '#f093fb'],
                        borderColor: ['#5a67d8', '#ed64a6'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Distribusi Stunting berdasarkan Jenis Kelamin',
                            font: {
                                size: 16
                            }
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Kasus'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Jenis Kelamin'
                            }
                        }
                    }
                }
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Show overview by default
            showSection('overview');
            
            // Add click event for mobile menu items
            document.querySelectorAll('.nav-menu a').forEach(link => {
                link.addEventListener('click', function() {
                    // Close mobile menu after selection
                    document.getElementById('nav-menu').classList.remove('active');
                });
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            // Refresh maps and charts on window resize
            if (mapsInitialized) {
                refreshMaps();
            }
            
            if (chartsInitialized) {
                setTimeout(() => {
                    if (trendChart) trendChart.resize();
                    if (villageChart) villageChart.resize();
                    if (ageChart) ageChart.resize();
                    if (genderChart) genderChart.resize();
                }, 100);
            }
        });
    </script>
</body>
</html>