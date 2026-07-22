import React from 'react';
import { createRoot } from 'react-dom/client';
import '../css/app.css';

import CharacterCreation from './components/CharacterCreation';
import BattlePage from './components/BattlePage';

const characterRoot = document.getElementById(
    'character-creation'
);

if (characterRoot) {
    createRoot(characterRoot).render(
        <React.StrictMode>
            <CharacterCreation
                action={characterRoot.dataset.action}
                csrfToken={characterRoot.dataset.csrf}
            />
        </React.StrictMode>
    );
}

const battleRoot = document.getElementById('battle-app');

if (battleRoot) {
    const battleDataElement =
        document.getElementById('battle-data');

    const initialBattle = JSON.parse(
        battleDataElement.textContent
    );

    createRoot(battleRoot).render(
    <BattlePage
        initialBattle={initialBattle}
        attackUrl={battleRoot.dataset.attackUrl}
        endUrl={battleRoot.dataset.endUrl}
    />
);
}