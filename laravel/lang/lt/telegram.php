<?php

return [
    'welcome' => "Sveiki atvykę. Šis botas prijungtas prie Relax administravimo duomenų.\n\nKomandos:\n/start\n/help\n/day 1\n/practice 1",
    'help' => "Galimos komandos:\n/start - parodyti trumpą įžangą\n/help - parodyti galimas komandas\n/day {number} - išvardyti aktyvias dienos praktikas\n/practice {id} - parodyti vieną praktiką",
    'unknown_command' => 'Nežinoma komanda. Naudokite /help, kad pamatytumėte galimas komandas.',
    'invalid_day' => 'Diena turi būti nuo 1 iki 29.',
    'day_empty' => ':day dienai aktyvių praktikų nerasta.',
    'day_intro' => 'Aktyvios praktikos :day dienai:',
    'practice_missing' => 'Praktika Nr. :id nerasta.',
    'api' => [
        'token_not_configured' => 'Telegram API tokenas nesukonfigūruotas.',
    ],
    'labels' => [
        'day' => 'Diena: :value',
        'duration' => 'Trukmė: :value',
        'focus_problem' => 'Pagrindinė problema: :value',
        'experience_level' => 'Patirties lygis: :value',
        'module_choice' => 'Modulio pasirinkimas: :value',
        'meditation_type' => 'Meditacijos tipas: :value',
    ],
];
