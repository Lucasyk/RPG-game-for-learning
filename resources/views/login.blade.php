<x-layout>
      <main class="flex min-h-screen flex-col gap-5 items-center justify-center bg-black p-5">
        @if($errors->any())
            <ul>
                <li class="text-red-500">
                    {{$errors->first()}}
                </li>
            </ul>
        @endif
  <form action="{{route('login')}}" method="post" class="flex flex-col gap-5 p-5 bg-gray-900 rounded-xl max-w-md w-full">
    @csrf
    <h1 class="text-white">Login</h1>
    <input type="text" name="email" placeholder="Email" class="text-white hover:bg-slate-800 rounded-xl pl-3">
    <input type="password" name="password" placeholder="Password" class="text-white hover:bg-slate-800 rounded-xl pl-3">
    <div class="flex justify-center items-center">
      <button type="submit" class="text-white hover:bg-emerald-200 rounded-xl hover:text-gray-900 w-36">Login</button>
    </div>
  </form>
  <div>
    <a href="/" class="text-white">◀ Back</a>
  </div>
</main>
</x-layout>