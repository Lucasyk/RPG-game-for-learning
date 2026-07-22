import { useState } from "react";

const characterClasses = {
  warrior: {
    label: "Warrior",
    icon: "🛡️",
    description: "Tough, physically strong...but hate study...",
    maxHp: 30,
    maxMp: 10,
    attack: 10,
    defense: 8,
    speed: 3
  },
   mage: {
    label: "Mage",
    icon: "🧙",
    description: "Powerful magic, questionable upper-body strength.",
    maxHp: 18,
    maxMp: 25,
    attack: 6,
    defense: 4,
    speed: 5
  },
   
   rogue: {
        label: 'Rogue',
        icon: '🗡️',
        description: 'Fast enough to steal your sandwich before lunch.',
     maxHp: 22,
        maxMp: 16,
        attack: 8,
        defense: 5,
        speed: 9,
    },
}

export default function CharacterCreation({action, csrfToken}) {
  const [name, setName] = useState("");
  const [selectedClass, setSelectedClass] = useState("warrior");

  const character = characterClasses[selectedClass];

  return (
        <main className="min-h-screen bg-gray-950 p-6 text-gray-100">
            <div className="mx-auto max-w-5xl">
                <header className="mb-8 text-center">
                    <h1 className="text-4xl font-bold">
                        Create Your Character
                    </h1>

                    <p className="mt-2 text-gray-400">
                        Choose wisely. The slimes are already doing push-ups.
                    </p>
                </header>

                <div className="grid gap-8 md:grid-cols-2">
                    <section className="rounded-2xl bg-gray-900 p-6">
                        <form method="POST" action={action}>
                            <input
                                type="hidden"
                                name="_token"
                                value={csrfToken}
                            />

                            <div className="mb-6">
                                <label
                                    htmlFor="name"
                                    className="mb-2 block font-semibold"
                                >
                                    Character name
                                </label>

                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    value={name}
                                    onChange={(e) =>
                                        setName(e.target.value)
                                    }
                                    maxLength={30}  
                                    required
                                    placeholder="Sir Crub"
                                    className="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 outline-none focus:border-blue-500"
                                />
                            </div>

                            <div>
                                <p className="mb-3 font-semibold">
                                    Choose a class
                                </p>

                                <div className="grid gap-3">
                                    {Object.entries(characterClasses).map(
                                        ([key, option]) => (
                                            <button
                                                key={key}
                                                type="button"
                                                onClick={() =>
                                                    setSelectedClass(key)
                                                }
                                                className={`rounded-xl border p-4 text-left transition ${
                                                    selectedClass === key
                                                        ? 'border-blue-500 bg-blue-950'
                                                        : 'border-gray-700 bg-gray-800 hover:border-gray-500'
                                                }`}
                                            >
                                                <span className="mr-3 text-2xl">
                                                    {option.icon}
                                                </span>

                                                <span className="font-bold">
                                                    {option.label}
                                                </span>

                                                <p className="mt-2 text-sm text-gray-400">
                                                    {option.description}
                                                </p>
                                            </button>
                                        )
                                    )}
                                </div>
                            </div>

                            <input
                                type="hidden"
                                name="character_class"
                                value={selectedClass}
                            />

                            <button
                                type="submit"
                                className="mt-8 w-full rounded-xl bg-blue-600 px-6 py-3 font-bold hover:bg-blue-500"
                            >
                                Create {name || 'Character'}
                            </button>
                        </form>
                    </section>

                    <section className="rounded-2xl bg-gray-900 p-6">
                        <div className="text-center">
                            <div className="text-8xl">
                                {character.icon}
                            </div>

                            <h2 className="mt-4 text-3xl font-bold">
                                {name || 'Unnamed Hero'}
                            </h2>

                            <p className="text-blue-400">
                                {character.label}
                            </p>
                        </div>

                        <div className="mt-8 space-y-4">
                            <Stat label="Max HP" value={character.maxHp} />
                            <Stat label="Max MP" value={character.maxMp} />
                            <Stat label="Attack" value={character.attack} />
                            <Stat label="Defense" value={character.defense} />
                            <Stat label="Speed" value={character.speed} />
                        </div>
                    </section>
                </div>
            </div>
        </main>
    );
}

function Stat({ label, value }) {
    return (
        <div className="flex items-center justify-between rounded-xl bg-gray-800 px-4 py-3">
            <span className="text-gray-400">{label}</span>
            <strong>{value}</strong>
        </div>
    );
}