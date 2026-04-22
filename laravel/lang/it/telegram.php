<?php

return [
    'welcome' => "Benvenuto. Questo bot è collegato ai dati amministrativi di Relax.\n\nComandi:\n/start\n/help\n/day 1\n/practice 1",
    'help' => "Comandi disponibili:\n/start - mostra una breve introduzione\n/help - mostra i comandi disponibili\n/day {number} - elenca le pratiche attive di un giorno\n/practice {id} - mostra una pratica",
    'unknown_command' => 'Comando sconosciuto. Usa /help per vedere i comandi disponibili.',
    'invalid_day' => 'Il giorno deve essere compreso tra 1 e 29.',
    'day_empty' => 'Non sono state trovate pratiche attive per il giorno :day.',
    'day_intro' => 'Pratiche attive per il giorno :day:',
    'practice_missing' => 'La pratica n. :id non è stata trovata.',
    'api' => [
        'token_not_configured' => 'Il token API di Telegram non è configurato.',
    ],
    'labels' => [
        'day' => 'Giorno: :value',
        'duration' => 'Durata: :value',
        'focus_problem' => 'Problema principale: :value',
        'experience_level' => 'Livello di esperienza: :value',
        'module_choice' => 'Scelta del modulo: :value',
        'meditation_type' => 'Tipo di meditazione: :value',
    ],
];
