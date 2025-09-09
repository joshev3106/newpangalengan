<div class=" bg-gray-50">
    <!-- Header -->
    @if(request()->routeIs('home'))
        <x-header></x-header>
    @endif

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
                 class="origin-top-right absolute mt-2 w-full shadow-lg bg-white ring-1 z-1200 ring-black/5">
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
                        
                        <x-nav-link :href="route('stunting.index')"
                                    :active="request()->routeIs('stunting.*')"
                                    class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm transform transition-all duration-200 whitespace-nowrap">
                            <span class="flex items-center gap-1 sm:gap-2">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
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

                        <x-nav-link :href="route('wilayah.index')" 
                                    :active="request()->routeIs('wilayah.index*')" 
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
                                <!-- Dokumen (outline basic) -->
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6M9 13h6M9 17h6"/>
                                </svg>
                                        <span>Laporan</span>
                            </span>
                        </x-nav-link>

                        @auth
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-nav-link :href="route('logout')">
                                    <button class="px-3 py-2 rounded-lg hover:bg-gray-100">Keluar</button>
                                </x-nav-link>
                            </form>
                        @endauth
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
                           class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm  whitespace-nowrap">
                    <span class="flex items-center gap-1 sm:gap-2">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Home</span>
                    </span>
                </x-nav-link>
                
                <x-nav-link :href="route('analisis-hotspot')" :active="request()->routeIs('analisis-hotspot')" 
                           class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm  whitespace-nowrap">
                    <span class="flex items-center gap-1 sm:gap-2">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Analisis Hotspot</span>
                    </span>
                </x-nav-link>
                
                <x-nav-link :href="route('stunting.index')"
                            :active="request()->routeIs('stunting.*')"
                            class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm whitespace-nowrap">
                    <span class="flex items-center gap-1 sm:gap-2">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        <span>Data Stunting</span>
                    </span>
                </x-nav-link>

                <x-nav-link :href="route('peta')" :active="request()->routeIs('peta')" 
                           class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm  whitespace-nowrap">
                    <span class="flex items-center gap-1 sm:gap-2">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        <span>Peta</span>
                    </span>
                </x-nav-link>

                <x-nav-link :href="route('wilayah.index')" 
                            :active="request()->routeIs('wilayah.index')" 
                            class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm  whitespace-nowrap">
                    <span class="flex items-center gap-1 sm:gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Data Wilayah</span>
                    </span>
                </x-nav-link>

                <x-nav-link :href="route('laporan')" :active="request()->routeIs('laporan')" 
                           class="group relative px-3 sm:px-6 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-lg shadow-sm  whitespace-nowrap">
                    <span class="flex items-center gap-1 sm:gap-2">
                        <!-- Dokumen (outline basic) -->
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6M9 13h6M9 17h6"/>
                        </svg>

                        <span>Laporan</span>
                    </span>
                </x-nav-link>

                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="px-3 py-2 rounded-lg hover:bg-gray-100">Keluar</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>
</div>
