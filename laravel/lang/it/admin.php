<?php

return [
    'navigation_groups' => [
        'content' => 'Contenuti',
        'categories' => 'Categorie',
        'daily_practices' => 'Pratiche giornaliere',
    ],
    'relation_managers' => [
        'practices' => 'Pratiche',
    ],
    'resources' => [
        'practices' => [
            'model' => 'pratica',
            'plural' => 'pratiche',
            'navigation' => 'Pratiche',
            'sections' => [
                'general' => 'Generale',
                'categorization' => 'Categorizzazione',
                'general_and_categorization' => 'Generale e categorizzazione',
                'media' => 'Media e durata',
                'translations' => 'Traduzioni',
            ],
            'fields' => [
                'day' => 'Giorno',
                'is_active' => 'Attiva',
                'focus_problem' => 'Problema principale',
                'experience_level' => 'Livello di esperienza',
                'module_choice' => 'Scelta del modulo',
                'meditation_type' => 'Tipo di meditazione',
                'duration' => 'Durata (secondi)',
                'image' => 'Immagine',
                'video' => 'File video',
                'title' => 'Titolo',
                'description' => 'Descrizione',
            ],
            'short_labels' => [
                'focus_problem' => 'Focus',
                'experience_level' => 'Livello',
                'module_choice' => 'Modulo',
                'meditation_type' => 'Tipo',
            ],
            'values' => [
                'day' => 'Giorno :day',
            ],
            'filters' => [
                'indicator' => ':label: :value',
            ],
        ],
        'languages' => [
            'model' => 'lingua',
            'plural' => 'lingue',
            'navigation' => 'Lingue',
            'sections' => [
                'details' => 'Dettagli lingua',
            ],
            'fields' => [
                'code' => 'Codice',
                'name' => 'Nome',
                'native_name' => 'Nome nativo',
                'is_enabled' => 'Attiva',
            ],
            'tabs' => [
                'all' => 'Tutte le lingue',
                'enabled' => 'Attive',
                'disabled' => 'Disattivate',
            ],
        ],
        'focus_problems' => [
            'model' => 'problema principale',
            'plural' => 'problemi principali',
            'navigation' => 'Problemi principali',
            'sections' => [
                'translations' => 'Traduzioni',
            ],
            'fields' => [
                'title' => 'Titolo',
            ],
        ],
        'experience_levels' => [
            'model' => 'livello di esperienza',
            'plural' => 'livelli di esperienza',
            'navigation' => 'Livelli di esperienza',
            'sections' => [
                'translations' => 'Traduzioni',
            ],
            'fields' => [
                'title' => 'Titolo',
            ],
        ],
        'module_choices' => [
            'model' => 'scelta del modulo',
            'plural' => 'scelte del modulo',
            'navigation' => 'Scelte del modulo',
            'sections' => [
                'translations' => 'Traduzioni',
            ],
            'fields' => [
                'title' => 'Titolo',
            ],
        ],
        'meditation_types' => [
            'model' => 'tipo di meditazione',
            'plural' => 'tipi di meditazione',
            'navigation' => 'Tipi di meditazione',
            'sections' => [
                'translations' => 'Traduzioni',
            ],
            'fields' => [
                'title' => 'Titolo',
            ],
        ],
    ],
    'widgets' => [
        'practice_overview' => [
            'heading' => 'Panoramica del programma',
            'description' => 'Copertura, prontezza dei media e ritmo della libreria di pratiche.',
            'stats' => [
                'total_practices' => [
                    'label' => 'Pratiche totali',
                    'description' => 'Elementi di pratica su tutti i giorni e le categorie',
                ],
                'enabled_languages' => [
                    'label' => 'Lingue attive',
                    'description' => 'Lingue attualmente disponibili nell’admin e nelle traduzioni',
                ],
                'days_covered' => [
                    'label' => 'Giorni coperti',
                    'description' => 'Giorni unici con almeno una pratica assegnata',
                ],
                'media_ready' => [
                    'label' => 'Media pronti',
                    'description' => ':percent% includono sia immagine sia video',
                ],
                'average_session' => [
                    'label' => 'Sessione media',
                    'description' => 'Durata media di tutte le pratiche pianificate',
                ],
            ],
        ],
        'practice_volume' => [
            'heading' => 'Volume delle pratiche per giorno',
            'description' => 'Come le pratiche sono distribuite nella dashboard di 29 giorni.',
            'dataset' => 'Pratiche',
        ],
        'practice_duration' => [
            'heading' => 'Durata media della sessione',
            'description' => 'Media dei minuti pianificati per ogni giorno del programma.',
            'dataset' => 'Minuti',
        ],
        'focus_problem_distribution' => [
            'heading' => 'Distribuzione dei problemi principali',
            'description' => 'Equilibrio delle pratiche tra le categorie di problemi principali.',
            'dataset' => 'Pratiche',
            'empty' => 'Nessuna pratica disponibile',
        ],
    ],
];
