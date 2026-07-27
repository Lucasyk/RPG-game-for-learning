<x-layout>
    <main class="flex min-h-screen flex-col items-center justify-center bg-black p-5">
        @if($errors->any())
            <ul>
                <li class="text-red-500">
                    {{$errors->first()}}
                </li>
            </ul>
        @endif
        <form
            action="{{ route('register') }}"
            method="POST"
            class="flex w-full max-w-md flex-col gap-4 rounded-xl bg-gray-900 p-8"
        >
            @csrf

            <h1 class="text-center text-3xl font-bold text-white">
                Create an account
            </h1>

            <input
                type="text"
                name="name"
                placeholder="Name"
                class="rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-white"
            >

            <input
                type="email"
                name="email"
                placeholder="Email"
                class="rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-white"
            >

            <input
                type="password"
                name="password"
                placeholder="Password"
                class="rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-white"
            >

            <button
                type="submit"
                class="rounded-lg bg-blue-600 px-4 py-3 font-bold text-white hover:bg-blue-500"
            >
                Register
            </button>

            <a href="{{route('loginShow')}}" class="font-bold text-white w-36 flex justify-center items-center">Already a user?</a>
        </form>
    </main>
</x-layout>