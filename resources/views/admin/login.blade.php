<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN Login - Sistem Informasi Data Stunting</title>
    <link rel="icon" href="img/logo-kab-bandung.png" type="image/png">
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-10">

    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-700 to-red-500 text-white text-center px-6 py-3 rounded-t-2xl shadow-lg">
            <h1 class="text-2xl font-bold">ADMIN | Login</h1>
            <p class="text-sm">Sistem Informasi Data Stunting</p>
            <p class="text-sm">Kecamatan Pangalengan</p>
        </div>

        <!-- Card Login -->
        <div class="bg-white shadow-lg rounded-b-2xl p-8">

            <!-- Form -->
            <form method="POST" action="{{ route('admin.login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-600 text-sm font-medium mb-2">Email</label>
                    <input type="email" name="email" id="email" required autofocus
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-gray-600 text-sm font-medium mb-2">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <!-- Button -->
                <button type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                    Login
                </button>
            </form>

            <!-- Extra Links -->
            {{-- <div class="mt-6 text-center">
                <a href="{{ route('password.request') }}" class="text-sm text-red-600 hover:underline">
                    Lupa Password?
                </a>
            </div> --}}
        </div>
    </div>

</body>
</html>
