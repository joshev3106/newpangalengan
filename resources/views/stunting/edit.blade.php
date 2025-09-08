<x-layout>
  <div class="max-w-3xl mx-auto p-6 bg-white rounded-2xl ring-1 ring-gray-100 mt-8">
    <h1 class="text-xl font-semibold mb-4">Edit Data Stunting</h1>

    @if ($errors->any())
      <div class="mb-4 rounded-lg bg-red-50 text-red-700 px-4 py-3">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('stunting.update', $stunting) }}" class="grid md:grid-cols-2 gap-4">
      @csrf @method('PUT')

      <div>
        <label class="block text-sm font-medium mb-1">Desa</label>
        <input name="desa" value="{{ old('desa', $stunting->desa) }}" required
               class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Kasus</label>
        <input type="number" min="0" name="kasus" value="{{ old('kasus', $stunting->kasus) }}" required
               class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Populasi</label>
        <input type="number" min="1" name="populasi" value="{{ old('populasi', $stunting->populasi) }}" required
               class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Periode (YYYY-MM)</label>
        <input type="month" name="period"
               value="{{ old('period', \Illuminate\Support\Carbon::parse($stunting->period)->format('Y-m')) }}"
               required class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500 hover:cursor-pointer">
      </div>

      <div class="md:col-span-2 mt-2 flex gap-2">
        <button class="px-4 py-2 rounded-lg bg-red-600 text-white hover:cursor-pointer hover:bg-red-500">Update</button>
        <a href="{{ route('stunting.index') }}" class="px-4 py-2 rounded-lg hover:cursor-pointer bg-gray-100">Batal</a>
      </div>
    </form>
  </div>
</x-layout>
