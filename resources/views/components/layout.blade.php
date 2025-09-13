@props(['title' => null])

@php
  use Illuminate\Support\Str;

  // Fallback judul dari nama route jika prop title tidak dikirim
  $routeName = Route::currentRouteName();
  $labels = [
    'home'             => 'Beranda',
    'stunting.index'   => 'Data Stunting',
    'stunting.create'  => 'Tambah Data Stunting',
    'stunting.edit'    => 'Edit Data Stunting',
    'wilayah.index'    => 'Data Wilayah',
    'wilayah.edit'     => 'Edit Profil Wilayah',
    'hotspot.index'    => 'Analisis Hotspot',
    'peta'             => 'Peta Faskes',
  ];
  $computedTitle = $title ?? ($labels[$routeName] ?? Str::headline(str_replace('.', ' ', $routeName)));
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Stunting | Kecamatan Pangalengan</title>
    <link rel="icon" href="img/logo-kab-bandung.png">
    <script src="//unpkg.com/alpinejs" defer></script>
    @vite('resources/css/app.css')
</head>
<body>
    @if(!request()->routeIs('login', 'stunting.create', 'stunting.edit', 'home', 'wilayah.edit'))
        <x-navbar></x-navbar>
    @endif

    <div>
        {{ $slot }}
    </div>

    <button id="toTopBtn" title="Go To Top"
      class="hidden fixed bottom-5 right-5 bg-red-600 hover:bg-red-700 text-white p-3 rounded-full shadow-lg transition hover:cursor-pointer z-1200">
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="w-6 h-6" fill="none" 
             viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M5 15l7-7 7 7" />
        </svg>
    </button>

    @stack('scripts')
    @stack('styles')
    @vite('resources/js/app.js')
</body>
</html>