<x-battle-layout>
  <main class="flex min-h-screen bg-black p-5 text-white flex-row gap-10">
    @if (session('success'))
    <p class="mb-4 text-green-400">
        {{ session('success') }}
    </p>
@endif

@if ($errors->any())
    <p class="mb-4 text-red-400">
        {{ $errors->first() }}
    </p>
@endif
    @if ($player)
    <a
        href="{{ route('battle.show') }}"
        class="rounded-xl bg-red-700 h-8 p-6 flex justify-center items-center font-bold text-white"
    >
        Enter Battle ⚔️
    </a>
@else
    <a
        href="{{ route('createPlayer') }}"
        class="rounded-xl bg-gray-900 h-8 p-6 flex justify-center items-center text-white"
    >
        Create Player
    </a>
@endif
    <a href="" class="bg-gray-900 h-8 p-6 flex justify-center items-center rounded-xl">
      Go on the journey
    </a>
  </main>
</x-battle-layout>