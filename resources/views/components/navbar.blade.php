<div class=" bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-red-800 via-red-700 to-red-800 shadow-lg">
        <!-- Top Bar -->
        <div class="bg-red-900/20 py-2 w-full" id="header">
            <div class="container mx-auto px-4 hidden md:block">
                <p class="text-center text-red-100 text-sm">
                    Sistem Informasi Data Stunting Kecamatan Pangalengan
                </p>
            </div>
            <div class="container mx-auto px-4 block md:hidden">
                <p class="text-center text-red-100 text-sm">
                    Sistem Informasi Data Stunting
                </p>
                <p class="text-center text-red-100 text-sm">
                    Kecamatan Pangalengan
                </p>
            </div>
        </div>
        
        <!-- Main Header -->
        <div class="container mx-auto px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 md:gap-6">
                <!-- Logo Section -->
                <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-6 text-center sm:text-left">
                    <div class="bg-white/10 backdrop-blur-sm rounded-full p-2 sm:p-3 border border-white/20">
                        <img src="img/logo-kab-bandung.png" alt="Logo Kabupaten Bandung" 
                             class="h-12 w-12 sm:h-16 sm:w-16 object-contain">
                    </div>
                    <div class="text-white">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold leading-tight">
                            Data Stunting
                        </h1>
                        <p class="text-red-200 text-base sm:text-lg font-medium">Kecamatan Pangalengan</p>
                    </div>
                </div>
                
                <!-- Location Info -->
                <div class="text-center md:text-right text-white w-full sm:w-auto">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg px-3 py-2 sm:px-4 sm:py-3 border border-white/20">
                        <p class="text-base sm:text-lg font-semibold">Kecamatan Pangalengan</p>
                        <p class="text-red-200 text-xs sm:text-sm">Kabupaten Bandung</p>
                        <p class="text-red-200 text-xs sm:text-sm">Provinsi Jawa Barat</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    {{-- Mobile View --}}
    <div class="block w-full md:hidden bg-red-700/90 justify-center content-center items-center pt-1" id="nav-mobile">
        <!-- Tambahin Alpine.js (sekali aja di layout.blade.php) -->

        <div class="relative inline-block text-left w-full" x-data="{ open: false }">
            <!-- Tombol Dropdown -->
            <button @click="open = !open"
                class="inline-flex justify-center w-full shadow-sm px-4 py-2 bg-red-700 text-sm font-medium text-white hover:cursor-pointer">
                {{-- <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg> --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 animated-gradient" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7m14 4l-7 7-7-7" />
                </svg>



            </button>
        
            <!-- Isi Dropdown -->
            <div x-show="open" 
                 @click.away="open = false"
                 x-transition 
                 class="origin-top-right absolute mt-2 w-full shadow-lg bg-white ring-1 ring-black/5">
                <div class="container mx-auto px-2 sm:px-4">
                    <div class="flex flex-col justify-center gap-1 sm:gap-2 py-3 sm:py-4 overflow-x-auto">
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')" 
                                class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm transform transition-all duration-200 whitespace-nowrap">
                            <span class="flex items-center gap-1 sm:gap-2">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <span>Home</span>
                            </span>
                        </x-nav-link>
                        
                        <x-nav-link :href="route('analisis-hotspot')" :active="request()->routeIs('analisis-hotspot')" 
                                class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm transform transition-all duration-200 whitespace-nowrap">
                            <span class="flex items-center gap-1 sm:gap-2">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span>Analisis Hotspot</span>
                            </span>
                        </x-nav-link>
                        
                        <x-nav-link :href="route('data-stunting')" :active="request()->routeIs('data-stunting')" 
                                class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm transform transition-all duration-200 whitespace-nowrap">
                            <span class="flex items-center gap-1 sm:gap-2">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <span>Data Stunting</span>
                            </span>
                        </x-nav-link>
                        
                        <x-nav-link :href="route('peta')" :active="request()->routeIs('peta')" 
                                class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm transform transition-all duration-200 whitespace-nowrap">
                            <span class="flex items-center gap-1 sm:gap-2">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                                <span>Peta</span>
                            </span>
                        </x-nav-link>

                        <x-nav-link :href="route('data-wilayah')" :active="request()->routeIs('data-wilayah')" 
                                class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm transform transition-all duration-200 whitespace-nowrap">
                            <span class="flex items-center gap-1 sm:gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span>Data Wilayah</span>
                            </span>
                        </x-nav-link>

                        <x-nav-link :href="route('laporan')" :active="request()->routeIs('laporan')" 
                                class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm transform transition-all duration-200 whitespace-nowrap">
                            <span class="flex items-center gap-1 sm:gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span>Laporan</span>
                            </span>
                        </x-nav-link>

                        @if (Auth::check() && Auth::user()->role === 'admin')
                            <x-nav-link :href="route('laporan')" :active="request()->routeIs('laporan')" 
                                    class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm transform transition-all duration-200 whitespace-nowrap">
                                <span class="flex items-center gap-1 sm:gap-2">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none">
                                      <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                                      <path d="M3 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" stroke="currentColor" stroke-width="2"/>
                                      <path d="M16 11l5 5-5 5M21 16H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="hover:cursor-pointer">
                                            Logout
                                        </button>
                                    </form>
                                </span>
                            </x-nav-link>                            
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Desktop View --}}
    <nav class="hidden md:block bg-white shadow-md border-t-4 border-red-600 w-full" id="navbar">
        <div class="container mx-auto px-2 sm:px-4">
            <div class="flex flex-wrap justify-center gap-1 sm:gap-2 py-3 sm:py-4 overflow-x-auto">
                <x-nav-link :href="route('home')" :active="request()->routeIs('home')" 
                           class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm transform hover:scale-105 transition-all duration-200 whitespace-nowrap">
                    <span class="flex items-center gap-1 sm:gap-2">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Home</span>
                    </span>
                </x-nav-link>
                
                <x-nav-link :href="route('analisis-hotspot')" :active="request()->routeIs('analisis-hotspot')" 
                           class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm transform hover:scale-105 transition-all duration-200 whitespace-nowrap">
                    <span class="flex items-center gap-1 sm:gap-2">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Analisis Hotspot</span>
                    </span>
                </x-nav-link>
                
                <x-nav-link :href="route('data-stunting')" :active="request()->routeIs('data-stunting')" 
                           class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm transform hover:scale-105 transition-all duration-200 whitespace-nowrap">
                    <span class="flex items-center gap-1 sm:gap-2">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <span>Data Stunting</span>
                    </span>
                </x-nav-link>
                
                <x-nav-link :href="route('peta')" :active="request()->routeIs('peta')" 
                           class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm transform hover:scale-105 transition-all duration-200 whitespace-nowrap">
                    <span class="flex items-center gap-1 sm:gap-2">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        <span>Peta</span>
                    </span>
                </x-nav-link>

                <x-nav-link :href="route('data-wilayah')" :active="request()->routeIs('data-wilayah')" 
                           class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm transform hover:scale-105 transition-all duration-200 whitespace-nowrap">
                    <span class="flex items-center gap-1 sm:gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Data Wilayah</span>
                    </span>
                </x-nav-link>

                <x-nav-link :href="route('laporan')" :active="request()->routeIs('laporan')" 
                           class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm transform hover:scale-105 transition-all duration-200 whitespace-nowrap">
                    <span class="flex items-center gap-1 sm:gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Laporan</span>
                    </span>
                </x-nav-link>

                @if (Auth::check() && Auth::user()->role === 'admin')
                    <x-nav-link :href="route('laporan')" :active="request()->routeIs('laporan')" 
                            class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm transform transition-all duration-200 whitespace-nowrap">
                        <span class="flex items-center gap-1 sm:gap-2">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none">
                              <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                              <path d="M3 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" stroke="currentColor" stroke-width="2"/>
                              <path d="M16 11l5 5-5 5M21 16H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="hover:cursor-pointer">
                                    Logout
                                </button>
                            </form>
                        </span>
                    </x-nav-link>                            
                @endif
            </div>
        </div>
    </nav>
</div>

@push('scripts')
<script>
    window.addEventListener("scroll", function() {
        console.log("discroll", window.scrollY)
        const navbar = document.getElementById("navbar");
        const navMobile = document.getElementById("nav-mobile");
        const header = document.getElementById("header");
        const scrollPoint = 200; // jumlah pixel scroll

        if (window.scrollY > scrollPoint) {
            navbar.classList.add("fixed", "top-0");
            navMobile.classList.add("fixed", "top-0");
        } else {
            navbar.classList.remove("fixed", "top-0");
            navMobile.classList.remove("fixed", "top-0");
        }
    });
</script>
@endpush