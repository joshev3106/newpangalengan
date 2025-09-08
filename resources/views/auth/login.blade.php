<x-layout>
    <div class="min-h-[90vh] flex items-center justify-center px-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6">
            <div class="text-red-700 flex justify-center font-semibold text-2xl mb-3">
                <p>Login <span>|</span> ADMIN</p>
            </div>

            @if (session('ok'))
                <div class="mb-3 rounded-lg bg-green-50 text-green-700 px-3 py-2">{{ session('ok') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-3 rounded-lg bg-red-50 text-red-700 px-3 py-2">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="p-2 w-full rounded-xl border border-gray-200 focus:border-red-500 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Kata Sandi</label>
                    <input type="password" name="password" required
                           class="p-2 w-full rounded-xl border border-gray-200 focus:border-red-500 focus:ring-red-500">
                </div>

                <div class="flex justify-center">
                    <a href="#" class="text-sm text-gray-500 hover:text-gray-700">Lupa sandi?</a>
                </div>

                <div class="flex w-full gap-2">
                    <button class="px-4 py-2 w-full rounded-lg bg-red-600 text-white hover:bg-red-700 hover:cursor-pointer">
                        Masuk
                    </button>
                    <a href="/" class="flex bg-red-600 hover:bg-red-700 justify-center text-white w-full rounded-lg items-center">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</x-layout>