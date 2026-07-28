import { useEffect, useRef, useState } from 'react';
const CLASS_SKILLS = {
    warrior: {
        name: 'Power Strike',
        icon: '💥',
        cost: 4,
    },

    mage: {
        name: 'Fireball',
        icon: '🔥',
        cost: 6,
    },

    rogue: {
        name: 'Double Slash',
        icon: '🗡️',
        cost: 5,
    },
};

function getClassSkill(characterClass) {
    return CLASS_SKILLS[characterClass] ?? {
        name: 'Class Skill',
        icon: '✨',
        cost: 0,
    };
}

export default function BattlePage({
    initialBattle,
    attackUrl,
    skillUrl,
    endUrl,
}) {
    const [battle, setBattle] = useState(initialBattle);
    const [isEnding, setIsEnding] = useState(false);
    const [isAttacking, setIsAttacking] = useState(false);
    const [error, setError] = useState('');
    const [isUsingSkill, setIsUsingSkill] = useState(false);
    const classSkill = getClassSkill(
    battle.player.character_class
);

const isBusy =
    isAttacking ||
    isUsingSkill;

    /*
    |--------------------------------------------------------------------------
    | Invalid battle data
    |--------------------------------------------------------------------------
    */

    if (!battle?.player || !battle?.enemy) {
        return (
            <main className="min-h-screen bg-gray-950 p-10 text-white">
                <h1 className="text-3xl font-bold text-red-400">
                    Battle data could not be loaded.
                </h1>

                <pre className="mt-6 whitespace-pre-wrap rounded-xl bg-black p-5">
                    {JSON.stringify(battle, null, 2)}
                </pre>
            </main>
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Skill
    |--------------------------------------------------------------------------
    */
    
    async function useSkill() {
    if (
        battle.status !== 'ongoing' ||
        isBusy
    ) {
        return;
    }

    setIsUsingSkill(true);
    setError('');

    try {
        const csrfToken = getCsrfToken();

        const response = await fetch(skillUrl, {
            method: 'POST',

            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },

            body: JSON.stringify({}),
        });

        const data = await readJsonResponse(response);

        if (!response.ok) {
            throw new Error(
                data.message ?? 'The skill failed.'
            );
        }

        if (!data.battle) {
            throw new Error(
                'Laravel did not return battle data.'
            );
        }

        setBattle(data.battle);
    } catch (skillError) {
        setError(
            skillError.message ??
                'Something went wrong while using the skill.'
        );
    } finally {
        setIsUsingSkill(false);
    }
}

    /*
    |--------------------------------------------------------------------------
    | Attack
    |--------------------------------------------------------------------------
    */

    async function attack() {
        if (
            battle.status !== 'ongoing' ||
            isAttacking
        ) {
            return;
        }

        setIsAttacking(true);
        setError('');

        try {
            const csrfToken = getCsrfToken();

            const response = await fetch(attackUrl, {
                method: 'POST',

                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },

                body: JSON.stringify({}),
            });

            const data = await readJsonResponse(response);

            if (!response.ok) {
                throw new Error(
                    data.message ?? 'The attack failed.'
                );
            }

            if (!data.battle) {
                throw new Error(
                    'Laravel did not return battle data.'
                );
            }

            setBattle(data.battle);
        } catch (attackError) {
            console.error('Attack error:', attackError);

            setError(
                attackError.message ??
                    'Something went wrong during the attack.'
            );
        } finally {
            setIsAttacking(false);
        }
    }
    
    

    /*
    |--------------------------------------------------------------------------
    | End battle
    |--------------------------------------------------------------------------
    */

    async function endBattle() {
        if (
            battle.status === 'ongoing' ||
            isEnding
        ) {
            return;
        }

        setIsEnding(true);
        setError('');

        try {
            const csrfToken = getCsrfToken();

            const response = await fetch(endUrl, {
                method: 'POST',

                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },

                body: JSON.stringify({}),
            });

            const data = await readJsonResponse(response);

            if (!response.ok) {
                throw new Error(
                    data.message ??
                        'Could not end the battle.'
                );
            }

            if (!data.redirect) {
                throw new Error(
                    'Laravel did not return a redirect URL.'
                );
            }

            window.location.href = data.redirect;
        } catch (endError) {
            console.error('End battle error:', endError);

            setError(
                endError.message ??
                    'Something went wrong while ending the battle.'
            );

            setIsEnding(false);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Page
    |--------------------------------------------------------------------------
    */

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
                        showStats
                    />

                    <FighterCard
                        title="Enemy"
                        fighter={battle.enemy}
                    />
                </section>

                {battle.status === 'won' &&
                    battle.rewards && (
                        <VictoryRewards
                            rewards={battle.rewards}
                        />
                    )}

                {error && (
                    <p className="mt-6 rounded-xl border border-red-700 bg-red-950 p-4 text-red-200">
                        {error}
                    </p>
                )}

                <section className="mt-8 flex justify-center gap-4">
                    {battle.status === 'ongoing' ? (
    <>
        <button
            type="button"
            onClick={attack}
            disabled={isBusy}
            className="rounded-xl bg-red-700 px-8 py-4 text-xl font-black transition hover:bg-red-600 disabled:cursor-not-allowed disabled:bg-gray-700"
        >
            {isAttacking
                ? 'Attacking...'
                : 'Attack ⚔️'}
        </button>

        <button
            type="button"
            onClick={useSkill}
            disabled={
                isBusy ||
                battle.player.mp < classSkill.cost
            }
            className="rounded-xl bg-purple-700 px-8 py-4 text-xl font-black transition hover:bg-purple-600 disabled:cursor-not-allowed disabled:bg-gray-700 disabled:text-gray-400"
        >
            {isUsingSkill
                ? 'Using Skill...'
                : `${classSkill.icon} ${classSkill.name} (${classSkill.cost} MP)`}
        </button>
    </>
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

                <BattleLog logs={battle.log ?? []} />
            </div>
        </main>
    );
}

/*
|--------------------------------------------------------------------------
| Fighter card
|--------------------------------------------------------------------------
*/

function getPlayerIcon(characterClass) {
    const icons = {
         warrior: '🛡️',
        mage: '🧙',
        rogue: '🗡️',
    }

    return icons[characterClass] ?? '🧑‍🚀';
}

function FighterCard({
    title,
    fighter,
    showStats = false,
}) {
    const hasLevel =
        fighter.level !== undefined &&
        fighter.level !== null;

    return (
        <article className="rounded-3xl border border-gray-800 bg-gray-900 p-6 shadow-2xl">
            <div className="text-center">
                <p className="text-sm uppercase tracking-widest text-gray-500">
                    {title}
                </p>

                {fighter.icon ? (
                    <img
                        src={fighter.icon}
                        alt={`${fighter.name} icon`}
                        className="mx-auto my-5 h-40 w-40 object-contain"
                    />
                ) : (
                    <div className="my-5 text-8xl">
                            {
                                fighter.character_class ? getPlayerIcon(fighter.character_class) : fighter.name
                        }
                    </div>
                )}

                <h2 className="text-3xl font-black">
                    {fighter.name}
                </h2>

                {hasLevel && (
                    <p className="mt-1 font-bold text-yellow-400">
                        Level {fighter.level}
                    </p>
                )}

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

            {fighter.max_mp !== undefined &&
                fighter.max_mp !== null && (
                    <ManaBar
                        mp={fighter.mp ?? 0}
                        maxMp={fighter.max_mp}
                    />
                )}

            {hasLevel && (
                <ExperienceBar
                    exp={fighter.exp ?? 0}
                    requiredExp={
                        fighter.exp_to_next_level ?? 100
                    }
                />
            )}

            {showStats && (
                <div className="mt-6 grid grid-cols-3 gap-3">
                    <Stat
                        label="ATK"
                        value={fighter.attack}
                    />

                    <Stat
                        label="DEF"
                        value={fighter.defense}
                    />

                    <Stat
                        label="SPD"
                        value={fighter.speed}
                    />
                </div>
            )}
        </article>
    );
}

/*
|--------------------------------------------------------------------------
| Health bar
|--------------------------------------------------------------------------
*/

function HealthBar({
    hp = 0,
    maxHp = 1,
}) {
    const safeMaxHp = maxHp > 0 ? maxHp : 1;

    const percentage = Math.max(
        0,
        Math.min(100, (hp / safeMaxHp) * 100)
    );

    let barClass = 'bg-green-500';

    if (percentage <= 50) {
        barClass = 'bg-yellow-500';
    }

    if (percentage <= 25) {
        barClass = 'bg-red-500';
    }

    return (
        <div>
            <div className="mb-2 flex justify-between font-bold">
                <span>HP</span>

                <span>
                    {hp} / {safeMaxHp}
                </span>
            </div>

            <div className="h-5 overflow-hidden rounded-full bg-gray-800">
                <div
                    className={`h-full transition-all duration-500 ${barClass}`}
                    style={{
                        width: `${percentage}%`,
                    }}
                />
            </div>
        </div>
    );
}

/*
|--------------------------------------------------------------------------
| Mana bar
|--------------------------------------------------------------------------
*/

function ManaBar({
    mp = 0,
    maxMp = 0,
}) {
    const safeMaxMp = maxMp > 0 ? maxMp : 1;

    const percentage =
        maxMp > 0
            ? Math.max(
                  0,
                  Math.min(
                      100,
                      (mp / safeMaxMp) * 100
                  )
              )
            : 0;

    return (
        <div className="mt-5">
            <div className="mb-2 flex justify-between font-bold">
                <span>MP</span>

                <span>
                    {mp} / {maxMp}
                </span>
            </div>

            <div className="h-4 overflow-hidden rounded-full bg-gray-800">
                <div
                    className="h-full bg-blue-500 transition-all duration-500"
                    style={{
                        width: `${percentage}%`,
                    }}
                />
            </div>
        </div>
    );
}

/*
|--------------------------------------------------------------------------
| Experience bar
|--------------------------------------------------------------------------
*/

function ExperienceBar({
    exp = 0,
    requiredExp = 100,
}) {
    const safeRequiredExp =
        requiredExp > 0 ? requiredExp : 100;

    const percentage = Math.max(
        0,
        Math.min(
            100,
            (exp / safeRequiredExp) * 100
        )
    );

    return (
        <div className="mt-5">
            <div className="mb-2 flex justify-between text-sm font-bold">
                <span>EXP</span>

                <span>
                    {exp} / {safeRequiredExp}
                </span>
            </div>

            <div className="h-3 overflow-hidden rounded-full bg-gray-800">
                <div
                    className="h-full bg-yellow-500 transition-all duration-500"
                    style={{
                        width: `${percentage}%`,
                    }}
                />
            </div>
        </div>
    );
}

/*
|--------------------------------------------------------------------------
| Stat box
|--------------------------------------------------------------------------
*/

function Stat({ label, value = 0 }) {
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

/*
|--------------------------------------------------------------------------
| Battle log
|--------------------------------------------------------------------------
*/

function BattleLog({ logs = [] }) {
    const logContainerRef = useRef(null);

    useEffect(() => {
        const container = logContainerRef.current;

        if (!container) {
            return;
        }

        container.scrollTo({
            top: container.scrollHeight,
            behavior: 'smooth',
        });
    }, [logs]);

    return (
        <section className="mt-8 rounded-3xl border border-gray-800 bg-black p-6">
            <h2 className="mb-4 text-xl font-black">
                Battle Log
            </h2>

            <div
                ref={logContainerRef}
                className="h-40 space-y-2 overflow-y-auto scroll-smooth pr-3 font-mono text-sm"
            >
                {logs.length > 0 ? (
                    logs.map((message, index) => (
                        <p
                            key={`${index}-${message}`}
                            className="border-l-2 border-red-700 pl-3 text-gray-300"
                        >
                            {message}
                        </p>
                    ))
                ) : (
                    <p className="text-gray-600">
                        The battle is suspiciously quiet...
                    </p>
                )}
            </div>
        </section>
    );
}

/*
|--------------------------------------------------------------------------
| Battle status
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Victory rewards
|--------------------------------------------------------------------------
*/

function VictoryRewards({ rewards }) {
    return (
        <section className="mt-8 rounded-2xl border border-yellow-700 bg-yellow-950 p-5 text-center">
            <h2 className="text-2xl font-black text-yellow-400">
                Victory Rewards
            </h2>

            <p className="mt-2 text-lg font-bold">
                +{rewards.exp_gained ?? 0} EXP
            </p>

            {(rewards.levels_gained ?? 0) > 0 && (
                <div className="mt-3">
                    <p className="text-2xl font-black text-green-400">
                        Level Up!
                    </p>

                    <p className="mt-1 font-bold">
                        Level {rewards.old_level}
                        {' → '}
                        Level {rewards.new_level}
                    </p>
                </div>
            )}
        </section>
    );
}

/*
|--------------------------------------------------------------------------
| Utilities
|--------------------------------------------------------------------------
*/

function getCsrfToken() {
    const token = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    if (!token) {
        throw new Error(
            'The CSRF token could not be found.'
        );
    }

    return token;
}

async function readJsonResponse(response) {
    const contentType =
        response.headers.get('content-type') ?? '';

    if (!contentType.includes('application/json')) {
        const text = await response.text();

        console.error('Laravel returned non-JSON:', text);

        throw new Error(
            `Expected JSON but received a ${response.status} response.`
        );
    }

    return response.json();
}