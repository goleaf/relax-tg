<?php

return [
    'navigation_groups' => [
        'content' => 'Turinys',
        'categories' => 'Kategorijos',
        'daily_practices' => 'Kasdienės praktikos',
    ],
    'relation_managers' => [
        'practices' => 'Praktikos',
    ],
    'resources' => [
        'practices' => [
            'model' => 'praktika',
            'plural' => 'praktikos',
            'navigation' => 'Praktikos',
            'sections' => [
                'general' => 'Bendra',
                'categorization' => 'Kategorizavimas',
                'general_and_categorization' => 'Bendra ir kategorizavimas',
                'media' => 'Medija ir trukmė',
                'translations' => 'Vertimai',
            ],
            'fields' => [
                'day' => 'Diena',
                'is_active' => 'Aktyvi',
                'focus_problem' => 'Pagrindinė problema',
                'experience_level' => 'Patirties lygis',
                'module_choice' => 'Modulio pasirinkimas',
                'meditation_type' => 'Meditacijos tipas',
                'duration' => 'Trukmė (sekundėmis)',
                'image' => 'Paveikslėlis',
                'video' => 'Vaizdo failas',
                'title' => 'Pavadinimas',
                'description' => 'Aprašymas',
            ],
            'short_labels' => [
                'focus_problem' => 'Fokusas',
                'experience_level' => 'Lygis',
                'module_choice' => 'Modulis',
                'meditation_type' => 'Tipas',
            ],
            'values' => [
                'day' => ':day diena',
            ],
            'filters' => [
                'indicator' => ':label: :value',
            ],
        ],
        'languages' => [
            'model' => 'kalba',
            'plural' => 'kalbos',
            'navigation' => 'Kalbos',
            'sections' => [
                'details' => 'Kalbos informacija',
            ],
            'fields' => [
                'code' => 'Kodas',
                'name' => 'Pavadinimas',
                'native_name' => 'Gimtasis pavadinimas',
                'is_enabled' => 'Įjungta',
            ],
            'tabs' => [
                'all' => 'Visos kalbos',
                'enabled' => 'Įjungtos',
                'disabled' => 'Išjungtos',
            ],
        ],
        'focus_problems' => [
            'model' => 'pagrindinė problema',
            'plural' => 'pagrindinės problemos',
            'navigation' => 'Pagrindinės problemos',
            'sections' => [
                'translations' => 'Vertimai',
            ],
            'fields' => [
                'title' => 'Pavadinimas',
            ],
        ],
        'experience_levels' => [
            'model' => 'patirties lygis',
            'plural' => 'patirties lygiai',
            'navigation' => 'Patirties lygiai',
            'sections' => [
                'translations' => 'Vertimai',
            ],
            'fields' => [
                'title' => 'Pavadinimas',
            ],
        ],
        'module_choices' => [
            'model' => 'modulio pasirinkimas',
            'plural' => 'modulio pasirinkimai',
            'navigation' => 'Modulio pasirinkimai',
            'sections' => [
                'translations' => 'Vertimai',
            ],
            'fields' => [
                'title' => 'Pavadinimas',
            ],
        ],
        'meditation_types' => [
            'model' => 'meditacijos tipas',
            'plural' => 'meditacijos tipai',
            'navigation' => 'Meditacijos tipai',
            'sections' => [
                'translations' => 'Vertimai',
            ],
            'fields' => [
                'title' => 'Pavadinimas',
            ],
        ],
    ],
    'widgets' => [
        'practice_overview' => [
            'heading' => 'Programos apžvalga',
            'description' => 'Aprėptis, medijos parengtis ir praktikos bibliotekos tempas.',
            'stats' => [
                'total_practices' => [
                    'label' => 'Iš viso praktikų',
                    'description' => 'Praktikų įrašai per visas dienas ir kategorijas',
                ],
                'enabled_languages' => [
                    'label' => 'Įjungtos kalbos',
                    'description' => 'Kalbos, kurios šiuo metu pasiekiamos administracijoje ir vertimuose',
                ],
                'days_covered' => [
                    'label' => 'Apimtos dienos',
                    'description' => 'Unikalios dienos, kurioms priskirta bent viena praktika',
                ],
                'media_ready' => [
                    'label' => 'Medija paruošta',
                    'description' => ':percent% turi ir paveikslėlį, ir vaizdo įrašą',
                ],
                'average_session' => [
                    'label' => 'Vidutinė sesija',
                    'description' => 'Vidutinė visų suplanuotų praktikų trukmė',
                ],
            ],
        ],
        'practice_volume' => [
            'heading' => 'Praktikų kiekis pagal dieną',
            'description' => 'Kaip praktikos pasiskirsto per 29 dienų skydelį.',
            'dataset' => 'Praktikos',
        ],
        'practice_duration' => [
            'heading' => 'Vidutinė sesijos trukmė',
            'description' => 'Vidutinis kiekvienai programos dienai suplanuotų minučių skaičius.',
            'dataset' => 'Minutės',
        ],
        'focus_problem_distribution' => [
            'heading' => 'Pagrindinių problemų pasiskirstymas',
            'description' => 'Praktikų balansas tarp pagrindinių problemų kategorijų.',
            'dataset' => 'Praktikos',
            'empty' => 'Praktikų dar nėra',
        ],
    ],
];
