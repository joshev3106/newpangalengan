<x-layout>
  <div class="max-w-3xl mx-auto p-6 bg-white rounded-2xl ring-1 ring-gray-100 mt-8">
    <h1 class="text-xl font-semibold mb-4">Tambah Data Stunting</h1>

    @if ($errors->any())
      <div class="mb-4 rounded-lg bg-red-50 text-red-700 px-4 py-3">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('stunting.store') }}" class="grid md:grid-cols-2 gap-4">
      @csrf

      <div>
        <label class="block text-sm font-medium mb-1">Desa</label>
        <input name="desa" value="{{ old('desa') }}" required
               class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Kasus</label>
        <input type="number" min="0" name="kasus" value="{{ old('kasus', 0) }}" required
               class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Populasi</label>
        <input type="number" min="1" name="populasi" value="{{ old('populasi', 1) }}" required
               class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Periode (YYYY-MM)</label>
          <input type="month" name="period" value="{{ old('period', $defaultPeriod) }}" required
                 class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">
      </div>

      <div class="md:col-span-2 mt-2 flex gap-2">
        <button class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-500 hover:cursor-pointer">Simpan</button>
        <a href="{{ route('stunting.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:cursor-pointer">Batal</a>
      </div>
    </form>
  </div>
</x-layout>
