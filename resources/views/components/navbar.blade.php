@props(['title' => null])

@php
  use Illuminate\Support\Str;

  // 1) Ambil judul dari prop `title` (kalau dikirim dari <x-layout title="...">)
  // 2) Kalau tidak ada, coba pakai variabel $pageTitle (kalau kamu set di halaman)
  // 3) Kalau tetap tidak ada, fallback dari nama route aktif
  $routeName = request()->route()?->getName() ?? '';
  $labels = [
    'home'             => 'Home',
    'stunting.index'   => 'Data Stunting',
    'stunting.create'  => 'Tambah Data Stunting',
    'stunting.edit'    => 'Edit Data Stunting',
    'wilayah.index'    => 'Data Wilayah',
    'wilayah.edit'     => 'Edit Profil Wilayah',
    'hotspot.index'    => 'Analisis Hotspot',
    'peta'             => 'Peta Faskes',
    'laporan'          => 'Laporan',
  ];
  // $pageTitle boleh ada/tidak—kalau tidak ada nilainya null saja
  $currentTitle = $title ?? ($pageTitle ?? ($labels[$routeName] ?? Str::headline(str_replace('.', ' ', $routeName))));
@endphp

<div class="bg-gray-50">
  {{-- ================= MOBILE ================= --}}
  <div class="md:hidden bg-gradient-to-r from-red-700/90 via-red-600 to-red-700/90" id="nav-mobile">
    <div class="relative inline-block w-full" x-data="{ open:false }">
      {{-- Tombol dropdown --}}
      <button
        @click="open = !open"
        class="inline-flex w-full items-center justify-center px-4 py-3 text-sm font-medium text-white shadow-sm
               bg-gradient-to-r from-red-700 via-red-600 to-red-700 transition-all duration-200 focus:outline-none"
        :class="{ 'brightness-110': open }"
        aria-controls="nav-mobile-menu"
        :aria-expanded="open.toString()"
      >
        <div class="w-full flex items-center justify-center gap-2">
          <span class="font-semibold truncate">{{ $currentTitle }} | Menu</span>
          <svg xmlns="http://www.w3.org/2000/svg"
               class="h-5 w-5 mt-0.5shrink-0 transform transition-transform duration-200"
               :class="{ 'rotate-180': open }"
               fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 9l-7 7-7-7m14-4l-7 7-7-7" />
          </svg>
        </div>

      </button>

      {{-- Isi dropdown --}}
      <div id="nav-mobile-menu"
           x-show="open" x-cloak @click.outside="open=false"
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="opacity-0 scale-95"
           x-transition:enter-end="opacity-100 scale-100"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="opacity-100 scale-100"
           x-transition:leave-end="opacity-0 scale-95"
           class="absolute left-0 right-0 top-full z-[1050] mt-1 w-full overflow-hidden rounded-b-lg bg-white shadow-lg"
      >
        <div class="px-2 py-2">
          <x-nav-link :href="route('home')" :active="request()->routeIs('home')"
                      class="group relative block border-b border-gray-100 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-red-600"
                      @click="open=false">
            <span class="flex items-center gap-3">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3" />
              </svg>
              <span>Home</span>
            </span>
          </x-nav-link>

          <x-nav-link :href="route('stunting.index')" :active="request()->routeIs('stunting.*')"
                      class="group relative block border-b border-gray-100 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-red-600"
                      @click="open=false">
            <span class="flex items-center gap-3">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197" />
              </svg>
              <span>Data Stunting</span>
            </span>
          </x-nav-link>

          <x-nav-link :href="route('wilayah.index')" :active="request()->routeIs('wilayah.*')"
                      class="group relative block border-b border-gray-100 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-red-600"
                      @click="open=false">
            <span class="flex items-center gap-3">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5M5 21H3m2 0h5" />
              </svg>
              <span>Data Wilayah</span>
            </span>
          </x-nav-link>

          <x-nav-link :href="route('hotspot.index')" :active="request()->routeIs('hotspot.*')"
                      class="group relative block border-b border-gray-100 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-red-600"
                      @click="open=false">
            <span class="flex items-center gap-3">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m4 0V9a2 2 0 012-2h2a2 2 0 012 2v10" />
              </svg>
              <span>Analisis Hotspot</span>
            </span>
          </x-nav-link>

          <x-nav-link :href="route('peta')" :active="request()->routeIs('peta')"
                      class="group relative block border-b border-gray-100 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-red-600"
                      @click="open=false">
            <span class="flex items-center gap-3">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618" />
              </svg>
              <span>Peta Faskes</span>
            </span>
          </x-nav-link>

          <x-nav-link :href="route('laporan')" :active="request()->routeIs('laporan')"
                      class="group relative block px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-red-600"
                      @click="open=false">
            <span class="flex items-center gap-3">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6M9 13h6M9 17h6" />
              </svg>
              <span>Laporan</span>
            </span>
          </x-nav-link>

          @auth
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit"
                      class="mt-1 w-full px-4 py-3 text-left text-sm font-medium text-red-600 hover:bg-red-50"
                      @click="open=false">
                <span class="flex items-center gap-3">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                  </svg>
                  <span>Keluar</span>
                </span>
              </button>
            </form>
          @endauth
        </div>
      </div>
    </div>
  </div>

  {{-- ================= DESKTOP ================= --}}
  <nav class="hidden md:block w-full border-t-4 border-red-600 bg-white shadow-md" id="navbar">
    <div class="container mx-auto px-2 sm:px-4">
      <div class="flex flex-wrap justify-center gap-1 sm:gap-2 py-3 sm:py-4 overflow-x-auto">
        <x-nav-link :href="route('home')" :active="request()->routeIs('home')"
                    class="group relative whitespace-nowrap rounded-lg px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium shadow-sm">
          <span class="flex items-center gap-1 sm:gap-2">
            <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3" />
            </svg>
            <span>Home</span>
          </span>
        </x-nav-link>

        <x-nav-link :href="route('stunting.index')" :active="request()->routeIs('stunting.*')"
                    class="group relative whitespace-nowrap rounded-lg px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium shadow-sm">
          <span class="flex items-center gap-1 sm:gap-2">
            <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1" />
            </svg>
            <span>Data Stunting</span>
          </span>
        </x-nav-link>

        <x-nav-link :href="route('wilayah.index')" :active="request()->routeIs('wilayah.*')"
                    class="group relative whitespace-nowrap rounded-lg px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium shadow-sm">
          <span class="flex items-center gap-1 sm:gap-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16" />
            </svg>
            <span>Data Wilayah</span>
          </span>
        </x-nav-link>

        <x-nav-link :href="route('hotspot.index')" :active="request()->routeIs('hotspot.*')"
                    class="group relative whitespace-nowrap rounded-lg px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium shadow-sm">
          <span class="flex items-center gap-1 sm:gap-2">
            <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m4 0V9a2 2 0 012-2h2a2 2 0 012 2v10" />
            </svg>
            <span>Analisis Hotspot</span>
          </span>
        </x-nav-link>

        <x-nav-link :href="route('peta')" :active="request()->routeIs('peta')"
                    class="group relative whitespace-nowrap rounded-lg px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium shadow-sm">
          <span class="flex items-center gap-1 sm:gap-2">
            <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618L9 7m0 13l6-3m0 0V4" />
            </svg>
            <span>Peta Faskes</span>
          </span>
        </x-nav-link>

        <x-nav-link :href="route('laporan')" :active="request()->routeIs('laporan')"
                    class="group relative whitespace-nowrap rounded-lg px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium shadow-sm">
          <span class="flex items-center gap-1 sm:gap-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6M9 13h6M9 17h6"/>
            </svg>
            <span>Laporan</span>
          </span>
        </x-nav-link>

        @auth
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="rounded-lg px-3 py-2 hover:bg-gray-100">Keluar</button>
          </form>
        @endauth
      </div>
    </div>
  </nav>
</div>
