<?php

return [
    'navigation_groups' => [
        'content' => 'Contenu',
        'categories' => 'Catégories',
        'daily_practices' => 'Pratiques quotidiennes',
    ],
    'relation_managers' => [
        'practices' => 'Pratiques',
    ],
    'resources' => [
        'practices' => [
            'model' => 'pratique',
            'plural' => 'pratiques',
            'navigation' => 'Pratiques',
            'sections' => [
                'general' => 'Général',
                'categorization' => 'Catégorisation',
                'general_and_categorization' => 'Général et catégorisation',
                'media' => 'Médias et durée',
                'translations' => 'Traductions',
            ],
            'fields' => [
                'day' => 'Jour',
                'is_active' => 'Actif',
                'focus_problem' => 'Problème ciblé',
                'experience_level' => 'Niveau d’expérience',
                'module_choice' => 'Choix du module',
                'meditation_type' => 'Type de méditation',
                'duration' => 'Durée (secondes)',
                'image' => 'Image',
                'video' => 'Fichier vidéo',
                'title' => 'Titre',
                'description' => 'Description',
            ],
            'short_labels' => [
                'focus_problem' => 'Cible',
                'experience_level' => 'Niveau',
                'module_choice' => 'Module',
                'meditation_type' => 'Type',
            ],
            'values' => [
                'day' => 'Jour :day',
            ],
            'filters' => [
                'indicator' => ':label : :value',
            ],
        ],
        'languages' => [
            'model' => 'langue',
            'plural' => 'langues',
            'navigation' => 'Langues',
            'sections' => [
                'details' => 'Détails de la langue',
            ],
            'fields' => [
                'code' => 'Code',
                'name' => 'Nom',
                'native_name' => 'Nom natif',
                'is_enabled' => 'Actif',
            ],
            'tabs' => [
                'all' => 'Toutes les langues',
                'enabled' => 'Actives',
                'disabled' => 'Désactivées',
            ],
        ],
        'focus_problems' => [
            'model' => 'problème ciblé',
            'plural' => 'problèmes ciblés',
            'navigation' => 'Problèmes ciblés',
            'sections' => [
                'translations' => 'Traductions',
            ],
            'fields' => [
                'title' => 'Titre',
            ],
        ],
        'experience_levels' => [
            'model' => 'niveau d’expérience',
            'plural' => 'niveaux d’expérience',
            'navigation' => 'Niveaux d’expérience',
            'sections' => [
                'translations' => 'Traductions',
            ],
            'fields' => [
                'title' => 'Titre',
            ],
        ],
        'module_choices' => [
            'model' => 'choix du module',
            'plural' => 'choix du module',
            'navigation' => 'Choix du module',
            'sections' => [
                'translations' => 'Traductions',
            ],
            'fields' => [
                'title' => 'Titre',
            ],
        ],
        'meditation_types' => [
            'model' => 'type de méditation',
            'plural' => 'types de méditation',
            'navigation' => 'Types de méditation',
            'sections' => [
                'translations' => 'Traductions',
            ],
            'fields' => [
                'title' => 'Titre',
            ],
        ],
    ],
    'widgets' => [
        'practice_overview' => [
            'heading' => 'Aperçu du programme',
            'description' => 'Couverture, disponibilité des médias et rythme dans la bibliothèque de pratiques.',
            'stats' => [
                'total_practices' => [
                    'label' => 'Pratiques totales',
                    'description' => 'Éléments de pratique sur l’ensemble des jours et des catégories',
                ],
                'enabled_languages' => [
                    'label' => 'Langues actives',
                    'description' => 'Langues actuellement disponibles dans l’administration et les traductions',
                ],
                'days_covered' => [
                    'label' => 'Jours couverts',
                    'description' => 'Jours uniques avec au moins une pratique assignée',
                ],
                'media_ready' => [
                    'label' => 'Médias prêts',
                    'description' => ':percent% incluent à la fois une image et une vidéo',
                ],
                'average_session' => [
                    'label' => 'Session moyenne',
                    'description' => 'Durée moyenne de toutes les pratiques planifiées',
                ],
            ],
        ],
        'practice_volume' => [
            'heading' => 'Volume des pratiques par jour',
            'description' => 'Répartition des pratiques sur le tableau de bord des 29 jours.',
            'dataset' => 'Pratiques',
        ],
        'practice_duration' => [
            'heading' => 'Durée moyenne des sessions',
            'description' => 'Nombre moyen de minutes prévues pour chaque jour du programme.',
            'dataset' => 'Minutes',
        ],
        'focus_problem_distribution' => [
            'heading' => 'Répartition des problèmes ciblés',
            'description' => 'Équilibre des pratiques entre les catégories de problèmes ciblés.',
            'dataset' => 'Pratiques',
            'empty' => 'Aucune pratique pour le moment',
        ],
    ],
];
