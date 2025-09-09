<x-layout>
    @push('styles')
        <style>
            .table-scroll { overflow: auto; }
            th.sticky { position: sticky; top: 0; background: #fff; z-index: 5; }
        </style>
    @endpush

    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- Flash message --}}
        @if (session('ok'))
            <div class="mb-4 rounded-lg bg-green-50 text-green-700 px-4 py-3">
                {{ session('ok') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 text-red-700 px-4 py-3">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center justify-between gap-4 mb-6">
            <div class="flex flex-wrap gap-2">
                @auth
                    <a href="{{ route('stunting.create') }}"
                       class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800">+ Tambah Data</a>
                @endauth
                {{-- sesuaikan route import/export jika sudah dibuat --}}
                {{-- <a href="#"
                   class="px-4 py-2 rounded-lg bg-white ring-1 ring-gray-200 hover:bg-gray-50">↥ Import CSV</a>
                <a href="#"
                   class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-500">↧ Export CSV</a> --}}
            </div>
        </div>

        {{-- Filter Bar (server-side) --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-4 md:p-5 mb-6">
            <form method="GET" action="{{ route('stunting.index') }}" id="filterForm" class="grid md:grid-cols-4 gap-3">
                <div class="relative">
                    <input name="q" id="q" type="text" placeholder="Cari desa …"
                           value="{{ $q ?? '' }}"
                           class="w-full rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500 pl-10">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
                    </svg>
                </div>

                <select name="severity" id="severity"
                        class="rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">
                    <option value="">Semua tingkat</option>
                    <option value="high"   @selected(($sev ?? '') === 'high')>Tinggi (&gt;20%)</option>
                    <option value="medium" @selected(($sev ?? '') === 'medium')>Sedang (10&ndash;20%)</option>
                    <option value="low"    @selected(($sev ?? '') === 'low')>Rendah (&lt;10%)</option>
                </select>

                <input name="from" id="dateFrom" type="month" value="{{ $from ?? '' }}"
                       class="rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">
                <input name="to" id="dateTo" type="month" value="{{ $to ?? '' }}"
                       class="rounded-xl p-2 border border-gray-200 focus:border-red-500 focus:ring-red-500">

                <div class="md:col-span-4 flex items-center gap-2 pt-1">
                    <button class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-500" type="submit">
                        Terapkan
                    </button>
                    <a href="{{ route('stunting.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100">
            <div class="table-scroll">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="text-gray-600 border-b">
                            <th class="sticky px-4 py-3 font-semibold">Desa</th>
                            <th class="sticky px-4 py-3 font-semibold">Kasus</th>
                            <th class="sticky px-4 py-3 font-semibold">Populasi</th>
                            <th class="sticky px-4 py-3 font-semibold">Tingkat</th>
                            <th class="sticky px-4 py-3 font-semibold">Periode</th>
                            @auth
                                <th class="sticky px-4 py-3 font-semibold">Aksi</th>
                            @endauth
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($rows as $d)
                            @php
                                // fallback jika accessor belum dibuat
                                $rate = isset($d->rate) ? $d->rate : ($d->populasi > 0 ? round(($d->kasus / $d->populasi) * 100, 1) : 0);
                                $sevRow = isset($d->severity) ? $d->severity : ($rate > 20 ? 'high' : ($rate >= 10 ? 'medium' : 'low'));
                                $clr = $sevRow === 'high' ? 'bg-red-600 text-white'
                                     : ($sevRow === 'medium' ? 'bg-orange-500 text-white' : 'bg-green-500 text-white');
                                $periodText = \Illuminate\Support\Carbon::parse($d->period)->format('Y-m');
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $d->desa }}</td>
                                <td class="px-4 py-3">{{ $d->kasus }}</td>
                                <td class="px-4 py-3">{{ number_format($d->populasi) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $clr }}">
                                        {{ number_format($rate, 1) }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $periodText }}</td>

                                @auth
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('stunting.edit', $d) }}" class="text-blue-600 hover:underline">Edit</a>
                                    <form action="{{ route('stunting.destroy', $d) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Hapus data ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline ml-2 hover:cursor-pointer">Hapus</button>
                                    </form>
                                </td>
                                @endauth
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer / pagination --}}
            <div class="px-4 py-3 border-t flex items-center justify-between text-sm text-gray-600">
                <div>
                    @if ($rows->total() > 0)
                        Menampilkan {{ $rows->firstItem() }}–{{ $rows->lastItem() }} dari {{ $rows->total() }} data
                    @else
                        0 data
                    @endif
                </div>
                <div>
                  {{ $rows->onEachSide(1)->links('pagination.red') }}
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Submit otomatis saat filter diubah (opsional)
            const f = document.getElementById('filterForm');
            ['severity','dateFrom','dateTo'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.addEventListener('change', () => f.submit());
            });
            // Enter di input q → submit
            const q = document.getElementById('q');
            if (q) q.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') f.submit();
            });
        </script>
    @endpush
</x-layout>
