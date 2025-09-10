<x-layout>
  <div class="max-w-3xl mx-auto p-6 bg-white rounded-2xl ring-1 ring-gray-100 mt-8">
    <h1 class="text-xl font-semibold mb-4">Tambah Hotspot</h1>

    @if ($errors->any())
      <div class="mb-4 rounded-lg bg-red-50 text-red-700 px-4 py-3">
        <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <form method="POST" action="{{ route('hotspot.store') }}" class="grid md:grid-cols-2 gap-4">
      @csrf
      <div>
        <label class="block text-sm font-medium mb-1">Nama Area *</label>
        <input name="name" value="{{ old('name') }}" required class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Confidence *</label>
        <select name="confidence" class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">
          <option value="0"  @selected(old('confidence')==='0')>0 - Not Significant</option>
          <option value="90" @selected(old('confidence')==='90')>90%</option>
          <option value="95" @selected(old('confidence')==='95')>95%</option>
          <option value="99" @selected(old('confidence')==='99')>99%</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Latitude *</label>
        <input type="number" step="any" name="lat" value="{{ old('lat') }}" required class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Longitude *</label>
        <input type="number" step="any" name="lng" value="{{ old('lng') }}" required class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Jumlah Kasus *</label>
        <input type="number" min="0" name="cases" value="{{ old('cases',0) }}" required class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">
      </div>

      <div class="md:col-span-2 mt-2 flex gap-2">
        <button class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-500">Simpan</button>
        <a href="{{ route('hotspot.index') }}" class="px-4 py-2 rounded-lg bg-gray-100">Batal</a>
      </div>
    </form>
  </div>
</x-layout>
