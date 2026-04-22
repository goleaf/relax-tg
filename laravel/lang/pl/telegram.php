<?php

return [
    'welcome' => "Witaj. Ten bot jest połączony z danymi administracyjnymi Relax.\n\nKomendy:\n/start\n/help\n/day 1\n/practice 1",
    'help' => "Dostępne komendy:\n/start - pokaż krótkie wprowadzenie\n/help - pokaż dostępne komendy\n/day {number} - wyświetl aktywne praktyki dla dnia\n/practice {id} - pokaż jedną praktykę",
    'unknown_command' => 'Nieznana komenda. Użyj /help, aby zobaczyć dostępne komendy.',
    'invalid_day' => 'Dzień musi mieścić się w zakresie od 1 do 29.',
    'day_empty' => 'Nie znaleziono aktywnych praktyk dla dnia :day.',
    'day_intro' => 'Aktywne praktyki dla dnia :day:',
    'practice_missing' => 'Nie znaleziono praktyki nr :id.',
    'api' => [
        'token_not_configured' => 'Token API Telegrama nie jest skonfigurowany.',
    ],
    'labels' => [
        'day' => 'Dzień: :value',
        'duration' => 'Czas trwania: :value',
        'focus_problem' => 'Główny problem: :value',
        'experience_level' => 'Poziom doświadczenia: :value',
        'module_choice' => 'Wybór modułu: :value',
        'meditation_type' => 'Typ medytacji: :value',
    ],
];
