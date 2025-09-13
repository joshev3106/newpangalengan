<x-layout>
  <div class="max-w-3xl mx-auto px-6 py-8">
    {{-- Breadcrumb / Back --}}
    <div class="mb-4 flex items-center justify-between">
      <div>
        <a href="{{ route('wilayah.index') }}" class="text-sm text-gray-600 hover:text-gray-800">← Kembali ke Data Wilayah</a>
        <h1 class="text-2xl font-semibold text-gray-900 mt-1">Edit Profil Desa</h1>
        <div class="text-sm text-gray-500 mt-0.5">Desa: <span class="font-medium text-gray-700">{{ $desa }}</span></div>
      </div>
      <div class="text-right">
        <div class="text-xs text-gray-500">Periode Terbaru</div>
        <div class="text-sm font-medium text-gray-700">
          {{ \Illuminate\Support\Carbon::parse($latest->period)->isoFormat("MMM 'YY") }}
        </div>
      </div>
    </div>

    {{-- Info cards --}}
    <div class="flex flex-col md:flex-row gap-3 mb-6">
      <div class="flex-1 bg-white rounded-2xl p-4 shadow-sm ring-1 ring-gray-100">
        <div class="text-xs text-gray-500">Populasi Terbaru</div>
        <div class="text-2xl font-bold text-gray-900">{{ number_format($latest->populasi) }}</div>
      </div>
      <div class="flex-1 bg-white rounded-2xl p-4 shadow-sm ring-1 ring-gray-100">
        <div class="text-xs text-gray-500">Kasus Terbaru</div>
        <div class="text-2xl font-bold text-gray-900">{{ number_format($latest->kasus) }}</div>
      </div>
      <div class="flex-1 bg-white rounded-2xl p-4 shadow-sm ring-1 ring-gray-100">
        @php
          $rate = $latest->populasi > 0 ? round(($latest->kasus / $latest->populasi) * 100, 1) : 0;
        @endphp
        <div class="text-xs text-gray-500">Tingkat (%)</div>
        <div class="text-2xl font-bold text-gray-900">{{ number_format($rate,1) }}%</div>
      </div>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100">
      <div class="px-5 py-4 border-b">
        <h2 class="font-semibold text-gray-800">Ubah Faskes & Pasien Dilayani</h2>
      </div>

      <form method="POST" action="{{ route('wilayah.update', $desa) }}" class="p-5 space-y-4">
        @csrf
        @method('PUT')

        {{-- Desa (readonly) --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Desa</label>
          <input type="text" value="{{ $desa }}" class="w-full rounded-xl p-2 border border-gray-200 bg-gray-100" disabled>
        </div>

        {{-- Populasi (readonly) --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Populasi Terbaru</label>
          <input type="text" value="{{ number_format($latest->populasi) }}" class="w-full rounded-xl p-2 border border-gray-200 bg-gray-100" disabled>
          <input type="hidden" id="populasiHidden" value="{{ (int)$latest->populasi }}">
        </div>

        {{-- Faskes Terdekat (text) --}}
        <div>
          <label for="faskes" class="block text-sm font-medium text-gray-700 mb-1">Faskes Terdekat</label>
          <input type="text" id="faskes" name="faskes" value="{{ old('faskes', $faskesText) }}"
                 placeholder="Contoh: Puskesmas Pangalengan"
                 class="w-full rounded-xl p-2 border border-gray-200 focus:border-blue-500 focus:ring-blue-500">
          <p class="text-xs text-gray-500 mt-1">Jika diisi, sistem akan menampilkan teks ini dan mengabaikan puskesmas_id yang pernah disetel.</p>
          @error('faskes') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Pasien Dilayani (served) --}}
        <div>
          <label for="served" class="block text-sm font-medium text-gray-700 mb-1">Pasien Dilayani</label>
          <input type="number" id="served" name="served" value="{{ old('served', $served) }}" min="0"
                 class="w-full rounded-xl p-2 border border-gray-200 focus:border-blue-500 focus:ring-blue-500">
          @error('served') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
          <div class="mt-1 text-xs text-gray-500">
            Perkiraan cakupan: <span id="cakupanPreview" class="font-medium">—</span>
          </div>
        </div>

        <div class="pt-2 flex items-center gap-2">
          <a href="{{ route('wilayah.index') }}"
             class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">Batal</a>
          <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  @push('scripts')
    <script>
      (function(){
        const served = document.getElementById('served');
        const pop    = parseInt(document.getElementById('populasiHidden').value || '0', 10);
        const out    = document.getElementById('cakupanPreview');

        function render(){
          const s = parseInt(served.value || '0', 10);
          if (!Number.isFinite(s) || pop <= 0) { out.textContent = '—'; return; }
          const pct = Math.max(0, Math.min(100, Math.round((s / pop) * 100)));
          out.textContent = pct + '%';
        }

        served.addEventListener('input', render);
        render();
      })();
    </script>
  @endpush
</x-layout>
