<?php

return [
    'navigation_groups' => [
        'content' => 'Treść',
        'categories' => 'Kategorie',
        'daily_practices' => 'Codzienne praktyki',
    ],
    'relation_managers' => [
        'practices' => 'Praktyki',
    ],
    'resources' => [
        'practices' => [
            'model' => 'praktyka',
            'plural' => 'praktyki',
            'navigation' => 'Praktyki',
            'sections' => [
                'general' => 'Ogólne',
                'categorization' => 'Kategoryzacja',
                'general_and_categorization' => 'Ogólne i kategoryzacja',
                'media' => 'Multimedia i czas trwania',
                'translations' => 'Tłumaczenia',
            ],
            'fields' => [
                'day' => 'Dzień',
                'is_active' => 'Aktywna',
                'focus_problem' => 'Główny problem',
                'experience_level' => 'Poziom doświadczenia',
                'module_choice' => 'Wybór modułu',
                'meditation_type' => 'Typ medytacji',
                'duration' => 'Czas trwania (sekundy)',
                'image' => 'Obraz',
                'video' => 'Plik wideo',
                'title' => 'Tytuł',
                'description' => 'Opis',
            ],
            'short_labels' => [
                'focus_problem' => 'Fokus',
                'experience_level' => 'Poziom',
                'module_choice' => 'Moduł',
                'meditation_type' => 'Typ',
            ],
            'values' => [
                'day' => 'Dzień :day',
            ],
            'filters' => [
                'indicator' => ':label: :value',
            ],
        ],
        'languages' => [
            'model' => 'język',
            'plural' => 'języki',
            'navigation' => 'Języki',
            'sections' => [
                'details' => 'Szczegóły języka',
            ],
            'fields' => [
                'code' => 'Kod',
                'name' => 'Nazwa',
                'native_name' => 'Nazwa rodzima',
                'is_enabled' => 'Włączony',
            ],
            'tabs' => [
                'all' => 'Wszystkie języki',
                'enabled' => 'Włączone',
                'disabled' => 'Wyłączone',
            ],
        ],
        'focus_problems' => [
            'model' => 'główny problem',
            'plural' => 'główne problemy',
            'navigation' => 'Główne problemy',
            'sections' => [
                'translations' => 'Tłumaczenia',
            ],
            'fields' => [
                'title' => 'Tytuł',
            ],
        ],
        'experience_levels' => [
            'model' => 'poziom doświadczenia',
            'plural' => 'poziomy doświadczenia',
            'navigation' => 'Poziomy doświadczenia',
            'sections' => [
                'translations' => 'Tłumaczenia',
            ],
            'fields' => [
                'title' => 'Tytuł',
            ],
        ],
        'module_choices' => [
            'model' => 'wybór modułu',
            'plural' => 'wybory modułu',
            'navigation' => 'Wybory modułu',
            'sections' => [
                'translations' => 'Tłumaczenia',
            ],
            'fields' => [
                'title' => 'Tytuł',
            ],
        ],
        'meditation_types' => [
            'model' => 'typ medytacji',
            'plural' => 'typy medytacji',
            'navigation' => 'Typy medytacji',
            'sections' => [
                'translations' => 'Tłumaczenia',
            ],
            'fields' => [
                'title' => 'Tytuł',
            ],
        ],
    ],
    'widgets' => [
        'practice_overview' => [
            'heading' => 'Przegląd programu',
            'description' => 'Zakres, gotowość multimediów i tempo biblioteki praktyk.',
            'stats' => [
                'total_practices' => [
                    'label' => 'Łączna liczba praktyk',
                    'description' => 'Pozycje praktyk we wszystkich dniach i kategoriach',
                ],
                'enabled_languages' => [
                    'label' => 'Włączone języki',
                    'description' => 'Języki obecnie dostępne w panelu administracyjnym i tłumaczeniach',
                ],
                'days_covered' => [
                    'label' => 'Objęte dni',
                    'description' => 'Unikalne dni z co najmniej jedną przypisaną praktyką',
                ],
                'media_ready' => [
                    'label' => 'Multimedia gotowe',
                    'description' => ':percent% zawiera zarówno obraz, jak i wideo',
                ],
                'average_session' => [
                    'label' => 'Średnia sesja',
                    'description' => 'Średni czas trwania wszystkich zaplanowanych praktyk',
                ],
            ],
        ],
        'practice_volume' => [
            'heading' => 'Liczba praktyk według dnia',
            'description' => 'Jak praktyki są rozłożone na 29-dniowym panelu.',
            'dataset' => 'Praktyki',
        ],
        'practice_duration' => [
            'heading' => 'Średnia długość sesji',
            'description' => 'Średnia liczba minut zaplanowanych na każdy dzień programu.',
            'dataset' => 'Minuty',
        ],
        'focus_problem_distribution' => [
            'heading' => 'Rozkład głównych problemów',
            'description' => 'Równowaga praktyk między kategoriami głównych problemów.',
            'dataset' => 'Praktyki',
            'empty' => 'Brak praktyk',
        ],
    ],
];
