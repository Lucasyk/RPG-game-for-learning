import { useState, useEffect, useRef } from 'react';

export default function BattlePage({
    initialBattle,
  attackUrl,
    endUrl
}) {
  const [battle, setBattle] = useState(initialBattle);
  const [isEnding, setIsEnding] = useState(false);
    const [isAttacking, setIsAttacking] = useState(false);
    const [error, setError] = useState('');

    async function attack() {
        if (battle.status !== 'ongoing' || isAttacking) {
            return;
        }

        setIsAttacking(true);
        setError('');

        try {
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content');

            const response = await fetch(attackUrl, {
                method: 'POST',

                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },

                body: JSON.stringify({}),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(
                    data.message ?? 'The attack failed.'
                );
            }

            setBattle(data.battle);
        } catch (attackError) {
            setError(attackError.message);
        } finally {
            setIsAttacking(false);
        }
  }
  
  async function endBattle() {
    if (battle.status === 'ongoing' || isEnding) {
        return;
    }

    setIsEnding(true);
    setError('');

    try {
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');

        const response = await fetch(endUrl, {
            method: 'POST',

            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },

            body: JSON.stringify({}),
        });

        const contentType =
            response.headers.get('content-type') ?? '';

        if (!contentType.includes('application/json')) {
            const text = await response.text();

            console.error('Laravel returned HTML:', text);

            throw new Error(
                `Expected JSON but received ${response.status} HTML response.`
            );
        }

        const data = await response.json();

        if (!response.ok) {
            throw new Error(
                data.message ?? 'Could not end the battle.'
            );
        }

        window.location.href = data.redirect;
    } catch (error) {
        setError(error.message);
        setIsEnding(false);
    }
}

    return (
        <main className="min-h-screen bg-gray-950 p-4 text-gray-100 md:p-8">
            <div className="mx-auto max-w-6xl">

                <header className="mb-8 text-center">
                    <p className="text-sm uppercase tracking-[0.4em] text-red-400">
                        Battle Arena
                    </p>

                    <h1 className="mt-2 text-4xl font-black md:text-6xl">
                        {battle.player.name}
                        <span className="mx-4 text-gray-600">
                            VS
                        </span>
                        {battle.enemy.name}
                    </h1>

                    <BattleStatus status={battle.status} />
                </header>

                <section className="grid gap-6 md:grid-cols-2">
                    <FighterCard
              title="Your Player"
              fighter={battle.player}
              symbol={battle.player.icon}
                    />

                    <FighterCard
                        title="Enemy"
                        fighter={battle.enemy}
                        symbol={battle.enemy.name}
                    />
                </section>

                {error && (
                    <p className="mt-6 rounded-xl border border-red-700 bg-red-950 p-4 text-red-200">
                        {error}
                    </p>
                )}

                <section className="mt-8 flex justify-center gap-4">
    {battle.status === 'ongoing' ? (
        <button
            type="button"
            onClick={attack}
            disabled={isAttacking}
            className="rounded-xl bg-red-700 px-10 py-4 text-xl font-black hover:bg-red-600 disabled:bg-gray-700"
        >
            {isAttacking ? 'Attacking...' : 'Attack ⚔️'}
        </button>
    ) : (
        <button
            type="button"
            onClick={endBattle}
            disabled={isEnding}
            className="rounded-xl bg-blue-700 px-10 py-4 text-xl font-black hover:bg-blue-600 disabled:bg-gray-700"
        >
            {isEnding
                ? 'Leaving...'
                : 'Return to Dashboard'}
        </button>
    )}
</section>

                <BattleLog logs={battle.log} />
            </div>
        </main>
    );
}

function FighterCard({ title, fighter, symbol }) {
    return (
        <article className="rounded-3xl border border-gray-800 bg-gray-900 p-6 shadow-2xl">
            <div className="text-center">
                <p className="text-sm uppercase tracking-widest text-gray-500">
                    {title}
                </p>

                <div className="my-5 text-8xl">
                    {symbol}
                </div>

                <h2 className="text-3xl font-black">
                    {fighter.name}
                </h2>

                {fighter.character_class && (
                    <p className="mt-1 capitalize text-blue-400">
                        {fighter.character_class}
                    </p>
                )}
            </div>

            <div className="mt-8">
                <HealthBar
                    hp={fighter.hp}
                    maxHp={fighter.max_hp}
                />
            </div>

            <div className="mt-6 grid grid-cols-3 gap-3">
                <Stat label="ATK" value={fighter.attack} />
                <Stat label="DEF" value={fighter.defense} />
                <Stat label="SPD" value={fighter.speed} />
            </div>
        </article>
    );
}

function HealthBar({ hp, maxHp }) {
    const percentage = maxHp > 0
        ? Math.max(0, Math.min(100, (hp / maxHp) * 100))
        : 0;

    return (
        <div>
            <div className="mb-2 flex justify-between font-bold">
                <span>HP</span>
                <span>
                    {hp} / {maxHp}
                </span>
            </div>

            <div className="h-5 overflow-hidden rounded-full bg-gray-800">
                <div
                    className="h-full bg-green-500 transition-all duration-500"
                    style={{
                        width: `${percentage}%`,
                    }}
                />
            </div>
        </div>
    );
}

function Stat({ label, value }) {
    return (
        <div className="rounded-xl bg-gray-800 p-3 text-center">
            <p className="text-xs font-bold text-gray-500">
                {label}
            </p>

            <p className="mt-1 text-xl font-black">
                {value}
            </p>
        </div>
    );
}

function BattleLog({ logs }) {
    const logContainerRef = useRef(null);

    useEffect(() => {
        const container = logContainerRef.current;

        if (container) {
            container.scrollTo({
                top: container.scrollHeight,
                behavior: 'smooth',
            });
        }
    }, [logs]);

    return (
        <section className="mt-8 rounded-3xl border border-gray-800 bg-black p-6">
            <h2 className="mb-4 text-xl font-black">
                Battle Log
            </h2>

            <div
                ref={logContainerRef}
                className="max-h-40 space-y-2 overflow-y-auto scroll-smooth pr-3 font-mono text-sm"
            >
                {logs.map((message, index) => (
                    <p
                        key={`${index}-${message}`}
                        className="border-l-2 border-red-700 pl-3 text-gray-300"
                    >
                        {message}
                    </p>
                ))}
            </div>
        </section>
    );
}

function BattleStatus({ status }) {
    if (status === 'won') {
        return (
            <p className="mt-4 text-2xl font-black text-green-400">
                Victory!
            </p>
        );
    }

    if (status === 'lost') {
        return (
            <p className="mt-4 text-2xl font-black text-red-500">
                Defeated!
            </p>
        );
    }

    return (
        <p className="mt-4 text-gray-400">
            Choose your next move.
        </p>
    );
}