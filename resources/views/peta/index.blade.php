<x-layout>
  @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
    <style>#map{height:70vh;min-height:480px}</style>
  @endpush

  <div class="max-w-7xl mx-auto p-6">
    <div id="map" class="rounded-2xl overflow-hidden shadow"></div>
  </div>

  @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
    <script>
      const markers = @json($markers);

      document.addEventListener('DOMContentLoaded', () => {
        const map = L.map('map').setView([-7.3167, 107.5833], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution:'© OpenStreetMap'
        }).addTo(map);

        const bounds = [];
        markers.forEach(m => {
          if (m.lat == null || m.lng == null) return;

          // Marker standar (ikon default Leaflet)
          const popup = `
            <div class="p-1">
              <div class="font-semibold text-gray-800">${m.puskesmas}</div>
              <div class="text-gray-800 text-sm">${m.address ?? '-'}</div>
              <div class="mt-1">
                <a href="https://www.google.com/maps?q=${m.lat},${m.lng}" target="_blank" rel="noopener"
                   class="text-blue-600 hover:underline">Buka di Google Maps</a>
              </div>
            </div>`;

          L.marker([m.lat, m.lng]).addTo(map).bindPopup(popup);
          bounds.push([m.lat, m.lng]);
        });

        if (bounds.length) map.fitBounds(bounds, { padding:[20,20] });
      });
    </script>
  @endpush
</x-layout>
