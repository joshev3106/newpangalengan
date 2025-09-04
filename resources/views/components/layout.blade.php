<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Data Stunting | Kecamatan Pangalengan</title>
    <link rel="icon" href="img/logo-kab-bandung.png" type="image/png">
    <script src="//unpkg.com/alpinejs" defer></script>
    @vite('resources/css/app.css')

  <style>
    @keyframes gradientMove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .animated-gradient {
      background: linear-gradient(270deg, #ff0080, #7928ca, #00c6ff);
      background-size: 600% 600%;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: gradientMove 5s ease infinite;
    }
  </style>
</head>
<body>
    <x-navbar></x-navbar>

    <div class="p-2">
        {{ $slot }}
    </div>

    @stack('scripts')
    @vite('resources/js/app.js')
</body>
</html>