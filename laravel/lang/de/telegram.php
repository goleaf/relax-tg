<?php

return [
    'welcome' => "Willkommen. Dieser Bot ist mit den Relax-Admin-Daten verbunden.\n\nBefehle:\n/start\n/help\n/day 1\n/practice 1",
    'help' => "Verfügbare Befehle:\n/start - kurze Einführung anzeigen\n/help - verfügbare Befehle anzeigen\n/day {number} - aktive Übungen für einen Tag auflisten\n/practice {id} - eine einzelne Übung anzeigen",
    'unknown_command' => 'Unbekannter Befehl. Verwende /help, um die verfügbaren Befehle zu sehen.',
    'invalid_day' => 'Der Tag muss zwischen 1 und 29 liegen.',
    'day_empty' => 'Für Tag :day wurden keine aktiven Übungen gefunden.',
    'day_intro' => 'Aktive Übungen für Tag :day:',
    'practice_missing' => 'Übung Nr. :id wurde nicht gefunden.',
    'api' => [
        'token_not_configured' => 'Das Telegram-API-Token ist nicht konfiguriert.',
    ],
    'labels' => [
        'day' => 'Tag: :value',
        'duration' => 'Dauer: :value',
        'focus_problem' => 'Fokusproblem: :value',
        'experience_level' => 'Erfahrungsstufe: :value',
        'module_choice' => 'Modulauswahl: :value',
        'meditation_type' => 'Meditationstyp: :value',
    ],
];
