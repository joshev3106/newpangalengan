<x-layout>
    @if (Auth::check() && Auth::user()->role === 'admin')
        <h1 class="h-[2000px]">Hai, ini home Admin</h1>
    @else
        <h1 class="h-[2000px]">Hai, ini home</h1>
    @endif
</x-layout>