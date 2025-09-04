<<<<<<< HEAD

=======
>>>>>>> 5e72e353cbdc1c231dc14cc870b4f7596b7ae72b
<x-layout>
    @if (Auth::check() && Auth::user()->role === 'admin')
        <h1 class="h-[2000px]">Hai, ini home Admin</h1>
    @else
        <h1 class="h-[2000px]">Hai, ini home</h1>
    @endif
<<<<<<< HEAD
</x-layout>
=======
</x-layout>
>>>>>>> 5e72e353cbdc1c231dc14cc870b4f7596b7ae72b
