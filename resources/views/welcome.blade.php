<x-layout>
    <main class="relative min-h-screen overflow-hidden bg-slate-950 text-white">
        {{-- Background effects --}}
        <div class="absolute inset-0 bg-radial-[at_top] from-purple-600/25 to-transparent to-45%"></div>

        <div class="absolute -left-32 top-32 h-72 w-72 rounded-full bg-purple-700/20 blur-3xl"></div>
        <div class="absolute -right-32 bottom-20 h-80 w-80 rounded-full bg-red-600/20 blur-3xl"></div>

        {{-- Navigation --}}
        <nav class="relative z-10 mx-auto flex max-w-7xl items-center justify-between px-6 py-6">
            <a
                href="{{ route('registerShow') }}"
                class="text-2xl font-black tracking-wider"
            >
                MONSTER<span class="text-purple-500">ARENA</span>
            </a>

            <div class="flex items-center gap-4">
                <a
                    href="{{ route('loginShow') }}"
                    class="text-sm font-semibold text-gray-300 transition hover:text-white"
                >
                    Login
                </a>

                <a
                    href="{{ route('registerShow') }}"
                    class="rounded-xl bg-purple-600 px-5 py-2.5 text-sm font-bold transition hover:bg-purple-500"
                >
                    Create Account
                </a>
            </div>
        </nav>

        {{-- Hero section --}}
        <section class="relative z-10 mx-auto grid min-h-[80vh] max-w-7xl items-center gap-12 px-6 py-16 lg:grid-cols-2">
            <div>
                <p class="mb-4 inline-flex rounded-full border border-purple-500/30 bg-purple-500/10 px-4 py-2 text-sm font-semibold text-purple-300">
                    ⚔️ The arena is now open
                </p>

                <h1 class="text-5xl font-black leading-tight sm:text-6xl lg:text-7xl">
                    Create your hero.
                    <span class="block bg-linear-to-r from-purple-400 via-pink-400 to-red-400 bg-clip-text text-transparent">
                        Defeat monsters.
                    </span>
                    Become suspiciously powerful.
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-8 text-gray-400">
                    Choose your class, build your stats, enter battle, earn EXP,
                    and prove that even a tiny slime should fear your name.
                </p>

                <div class="mt-10 flex flex-wrap gap-4">
                    <a
                        href="{{ route('registerShow') }}"
                        class="rounded-xl bg-purple-600 px-7 py-4 font-bold shadow-lg shadow-purple-900/40 transition hover:-translate-y-1 hover:bg-purple-500"
                    >
                        Start Your Adventure
                    </a>

                    <a
                        href="{{ route('loginShow') }}"
                        class="rounded-xl border border-gray-700 bg-gray-900/70 px-7 py-4 font-bold transition hover:border-gray-500 hover:bg-gray-800"
                    >
                        Continue Battle
                    </a>
                </div>

                <div class="mt-10 flex flex-wrap gap-6 text-sm text-gray-500">
                    <span>🛡️ Choose a class</span>
                    <span>👹 Fight enemies</span>
                    <span>✨ Earn EXP</span>
                </div>
            </div>

            {{-- Battle preview card --}}
            <div class="relative">
                <div class="absolute inset-0 rotate-3 rounded-3xl bg-linear-to-br from-purple-600 to-red-600 opacity-40 blur-xl"></div>

                <div class="relative rounded-3xl border border-gray-800 bg-gray-900/90 p-6 shadow-2xl backdrop-blur">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-widest text-gray-500">
                                Battle Preview
                            </p>

                            <h2 class="text-2xl font-bold">
                                Forest Encounter
                            </h2>
                        </div>

                        <span class="rounded-full bg-red-500/10 px-3 py-1 text-sm font-semibold text-red-400">
                            LIVE
                        </span>
                    </div>

                    <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-4">
                        <div class="rounded-2xl bg-gray-800 p-5 text-center">
                            <div class="text-7xl">🧙</div>

                            <h3 class="mt-3 text-xl font-bold">
                                Arcane Lucas
                            </h3>

                            <p class="text-sm text-purple-400">
                                Mage
                            </p>

                            <div class="mt-4 h-3 overflow-hidden rounded-full bg-gray-700">
                                <div class="h-full w-4/5 bg-green-500"></div>
                            </div>

                            <p class="mt-2 text-sm text-gray-400">
                                HP 18 / 22
                            </p>
                        </div>

                        <div class="text-3xl font-black text-red-500">
                            VS
                        </div>

                        <div class="rounded-2xl bg-gray-800 p-5 text-center">
                            <div class="text-7xl">👺</div>

                            <h3 class="mt-3 text-xl font-bold">
                                Tax Goblin
                            </h3>

                            <p class="text-sm text-red-400">
                                Enemy
                            </p>

                            <div class="mt-4 h-3 overflow-hidden rounded-full bg-gray-700">
                                <div class="h-full w-2/5 bg-red-500"></div>
                            </div>

                            <p class="mt-2 text-sm text-gray-400">
                                HP 9 / 25
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-2xl bg-black/60 p-4 font-mono text-sm text-green-400">
                        <p>&gt; Arcane Lucas cast Fireball!</p>
                        <p>&gt; Tax Goblin took 12 damage.</p>
                        <p>&gt; Tax Goblin is reconsidering its career.</p>
                    </div>

                    <button
                        type="button"
                        class="mt-6 w-full rounded-xl bg-red-700 py-3 font-bold transition hover:bg-red-600"
                    >
                        ⚔️ Attack
                    </button>
                </div>
            </div>
        </section>

        {{-- Features --}}
        <section class="relative z-10 border-t border-gray-900 bg-black/20 px-6 py-20">
            <div class="mx-auto max-w-7xl">
                <div class="grid gap-6 md:grid-cols-3">
                    <article class="rounded-2xl border border-gray-800 bg-gray-900/70 p-6">
                        <div class="text-4xl">🧙</div>

                        <h3 class="mt-4 text-xl font-bold">
                            Create Your Character
                        </h3>

                        <p class="mt-2 text-gray-400">
                            Pick a class and build a fighter with unique stats,
                            strengths, and questionable fashion choices.
                        </p>
                    </article>

                    <article class="rounded-2xl border border-gray-800 bg-gray-900/70 p-6">
                        <div class="text-4xl">⚔️</div>

                        <h3 class="mt-4 text-xl font-bold">
                            Strategic Battles
                        </h3>

                        <p class="mt-2 text-gray-400">
                            Attack, defend, heal, and survive enemies that become
                            progressively less polite.
                        </p>
                    </article>

                    <article class="rounded-2xl border border-gray-800 bg-gray-900/70 p-6">
                        <div class="text-4xl">🏆</div>

                        <h3 class="mt-4 text-xl font-bold">
                            Grow Stronger
                        </h3>

                        <p class="mt-2 text-gray-400">
                            Earn EXP, increase your stats, and eventually bully
                            the goblin who laughed at level one.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <footer class="relative z-10 border-t border-gray-900 px-6 py-8 text-center text-sm text-gray-600">
            Monster Arena — built with Laravel, React, and lazy person with some inspirations.
        </footer>
    </main>
</x-layout>