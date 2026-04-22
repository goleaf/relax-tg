<?php

return [
    'navigation_groups' => [
        'content' => 'Inhalte',
        'categories' => 'Kategorien',
        'daily_practices' => 'Tägliche Übungen',
    ],
    'relation_managers' => [
        'practices' => 'Übungen',
    ],
    'resources' => [
        'practices' => [
            'model' => 'übung',
            'plural' => 'übungen',
            'navigation' => 'Übungen',
            'sections' => [
                'general' => 'Allgemein',
                'categorization' => 'Kategorisierung',
                'general_and_categorization' => 'Allgemein und Kategorisierung',
                'media' => 'Medien und Dauer',
                'translations' => 'Übersetzungen',
            ],
            'fields' => [
                'day' => 'Tag',
                'is_active' => 'Aktiv',
                'focus_problem' => 'Fokusproblem',
                'experience_level' => 'Erfahrungsstufe',
                'module_choice' => 'Modulauswahl',
                'meditation_type' => 'Meditationstyp',
                'duration' => 'Dauer (Sekunden)',
                'image' => 'Bild',
                'video' => 'Videodatei',
                'title' => 'Titel',
                'description' => 'Beschreibung',
            ],
            'short_labels' => [
                'focus_problem' => 'Fokus',
                'experience_level' => 'Level',
                'module_choice' => 'Modul',
                'meditation_type' => 'Typ',
            ],
            'values' => [
                'day' => ':day. Tag',
            ],
            'filters' => [
                'indicator' => ':label: :value',
            ],
        ],
        'languages' => [
            'model' => 'sprache',
            'plural' => 'sprachen',
            'navigation' => 'Sprachen',
            'sections' => [
                'details' => 'Sprachdetails',
            ],
            'fields' => [
                'code' => 'Code',
                'name' => 'Name',
                'native_name' => 'Eigenbezeichnung',
                'is_enabled' => 'Aktiv',
            ],
            'tabs' => [
                'all' => 'Alle Sprachen',
                'enabled' => 'Aktiv',
                'disabled' => 'Deaktiviert',
            ],
        ],
        'focus_problems' => [
            'model' => 'fokusproblem',
            'plural' => 'fokusprobleme',
            'navigation' => 'Fokusprobleme',
            'sections' => [
                'translations' => 'Übersetzungen',
            ],
            'fields' => [
                'title' => 'Titel',
            ],
        ],
        'experience_levels' => [
            'model' => 'erfahrungsstufe',
            'plural' => 'erfahrungsstufen',
            'navigation' => 'Erfahrungsstufen',
            'sections' => [
                'translations' => 'Übersetzungen',
            ],
            'fields' => [
                'title' => 'Titel',
            ],
        ],
        'module_choices' => [
            'model' => 'modulauswahl',
            'plural' => 'modulauswahlen',
            'navigation' => 'Modulauswahlen',
            'sections' => [
                'translations' => 'Übersetzungen',
            ],
            'fields' => [
                'title' => 'Titel',
            ],
        ],
        'meditation_types' => [
            'model' => 'meditationstyp',
            'plural' => 'meditationstypen',
            'navigation' => 'Meditationstypen',
            'sections' => [
                'translations' => 'Übersetzungen',
            ],
            'fields' => [
                'title' => 'Titel',
            ],
        ],
    ],
    'widgets' => [
        'practice_overview' => [
            'heading' => 'Programmsnapshot',
            'description' => 'Abdeckung, Medienbereitschaft und Taktung der Übungsbibliothek.',
            'stats' => [
                'total_practices' => [
                    'label' => 'Übungen gesamt',
                    'description' => 'Übungseinträge über alle Tage und Kategorien hinweg',
                ],
                'enabled_languages' => [
                    'label' => 'Aktive Sprachen',
                    'description' => 'Sprachen, die derzeit im Adminbereich und in Übersetzungen verfügbar sind',
                ],
                'days_covered' => [
                    'label' => 'Abgedeckte Tage',
                    'description' => 'Eindeutige Tage mit mindestens einer zugewiesenen Übung',
                ],
                'media_ready' => [
                    'label' => 'Medienbereit',
                    'description' => ':percent% enthalten sowohl Bild als auch Video',
                ],
                'average_session' => [
                    'label' => 'Durchschnittliche Sitzung',
                    'description' => 'Mittlere Dauer aller geplanten Übungen',
                ],
            ],
        ],
        'practice_volume' => [
            'heading' => 'Übungsvolumen nach Tag',
            'description' => 'Wie Übungseinträge über das 29-Tage-Dashboard verteilt sind.',
            'dataset' => 'Übungen',
        ],
        'practice_duration' => [
            'heading' => 'Durchschnittliche Sitzungsdauer',
            'description' => 'Durchschnittliche Minuten, die für jeden Programmtag geplant sind.',
            'dataset' => 'Minuten',
        ],
        'focus_problem_distribution' => [
            'heading' => 'Verteilung der Fokusprobleme',
            'description' => 'Balance der Übungen über die Kategorien der Fokusprobleme hinweg.',
            'dataset' => 'Übungen',
            'empty' => 'Noch keine Übungen',
        ],
    ],
];
