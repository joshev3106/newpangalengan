<x-layout>
  <div class="max-w-4xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-xl md:text-2xl font-semibold text-gray-900">Edit Data Stunting</h1>
        <p class="text-sm text-gray-500 mt-1">Perbarui data kasus, populasi, dan periode untuk desa terkait.</p>
      </div>
      <a href="{{ route('stunting.index') }}"
         class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 hover:cursor-pointer">
        <span class="text-lg">←</span>
        <span>Kembali</span>
      </a>
    </div>

    {{-- Error --}}
    @if ($errors->any())
      <div class="mb-4 rounded-lg bg-red-50 text-red-700 px-4 py-3">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100">
      <form method="POST" action="{{ route('stunting.update', $stunting) }}" class="p-5 md:p-6">
        @csrf
        @method('PUT')

        <div class="grid md:grid-cols-2 gap-4">

          {{-- Desa (dikunci saat edit) --}}
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Desa</label>

            {{-- jika dikunci, tetap kirim value via hidden --}}
            @if(!empty($lockDesa))
              <input type="hidden" name="desa" value="{{ old('desa', $stunting->desa) }}">
            @endif

            <select
              name="{{ !empty($lockDesa) ? 'desa_locked' : 'desa' }}"
              {{ !empty($lockDesa) ? 'disabled' : '' }}
              class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500 bg-white">
              <option value="">— Pilih desa —</option>
              @foreach ($desaOptions as $desa)
                <option value="{{ $desa }}" @selected(old('desa', $stunting->desa) === $desa)>{{ $desa }}</option>
              @endforeach
            </select>

            @if(!empty($lockDesa))
              <p class="text-xs text-gray-500 mt-1">Nama desa dikunci pada halaman edit.</p>
            @endif
          </div>

          {{-- Kasus --}}
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kasus</label>
            <input type="number" min="0" name="kasus" value="{{ old('kasus', $stunting->kasus) }}" required
                   class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500 bg-white">
          </div>

          {{-- Populasi --}}
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Populasi</label>
            <input type="number" min="1" name="populasi" value="{{ old('populasi', $stunting->populasi) }}" required
                   class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500 bg-white">
          </div>

          {{-- Periode (YYYY-MM) --}}
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Periode (YYYY-MM)</label>
            <input type="month" name="period"
                   value="{{ old('period', \Illuminate\Support\Carbon::parse($stunting->period)->format('Y-m')) }}"
                   required
                   class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500 bg-white hover:cursor-pointer">
          </div>

        </div>

        {{-- Actions --}}
        <div class="mt-5 flex flex-col-reverse sm:flex-row gap-2 justify-end">
          <a href="{{ route('stunting.index') }}"
             class="px-4 py-2 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 hover:cursor-pointer">
            Batal
          </a>
          <button
            class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-500 hover:cursor-pointer">
            Update
          </button>
        </div>
      </form>
    </div>
  </div>
</x-layout>
