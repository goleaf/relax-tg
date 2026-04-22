<?php

return [
    'navigation_groups' => [
        'content' => 'Contenido',
        'categories' => 'Categorías',
        'daily_practices' => 'Prácticas diarias',
    ],
    'relation_managers' => [
        'practices' => 'Prácticas',
    ],
    'resources' => [
        'practices' => [
            'model' => 'práctica',
            'plural' => 'prácticas',
            'navigation' => 'Prácticas',
            'sections' => [
                'general' => 'General',
                'categorization' => 'Categorización',
                'general_and_categorization' => 'General y categorización',
                'media' => 'Medios y duración',
                'translations' => 'Traducciones',
            ],
            'fields' => [
                'day' => 'Día',
                'is_active' => 'Activo',
                'focus_problem' => 'Problema central',
                'experience_level' => 'Nivel de experiencia',
                'module_choice' => 'Selección de módulo',
                'meditation_type' => 'Tipo de meditación',
                'duration' => 'Duración (segundos)',
                'image' => 'Imagen',
                'video' => 'Archivo de video',
                'title' => 'Título',
                'description' => 'Descripción',
            ],
            'short_labels' => [
                'focus_problem' => 'Enfoque',
                'experience_level' => 'Nivel',
                'module_choice' => 'Módulo',
                'meditation_type' => 'Tipo',
            ],
            'values' => [
                'day' => 'Día :day',
            ],
            'filters' => [
                'indicator' => ':label: :value',
            ],
        ],
        'languages' => [
            'model' => 'idioma',
            'plural' => 'idiomas',
            'navigation' => 'Idiomas',
            'sections' => [
                'details' => 'Detalles del idioma',
            ],
            'fields' => [
                'code' => 'Código',
                'name' => 'Nombre',
                'native_name' => 'Nombre nativo',
                'is_enabled' => 'Activo',
            ],
            'tabs' => [
                'all' => 'Todos los idiomas',
                'enabled' => 'Activos',
                'disabled' => 'Desactivados',
            ],
        ],
        'focus_problems' => [
            'model' => 'problema central',
            'plural' => 'problemas centrales',
            'navigation' => 'Problemas centrales',
            'sections' => [
                'translations' => 'Traducciones',
            ],
            'fields' => [
                'title' => 'Título',
            ],
        ],
        'experience_levels' => [
            'model' => 'nivel de experiencia',
            'plural' => 'niveles de experiencia',
            'navigation' => 'Niveles de experiencia',
            'sections' => [
                'translations' => 'Traducciones',
            ],
            'fields' => [
                'title' => 'Título',
            ],
        ],
        'module_choices' => [
            'model' => 'selección de módulo',
            'plural' => 'selecciones de módulo',
            'navigation' => 'Selecciones de módulo',
            'sections' => [
                'translations' => 'Traducciones',
            ],
            'fields' => [
                'title' => 'Título',
            ],
        ],
        'meditation_types' => [
            'model' => 'tipo de meditación',
            'plural' => 'tipos de meditación',
            'navigation' => 'Tipos de meditación',
            'sections' => [
                'translations' => 'Traducciones',
            ],
            'fields' => [
                'title' => 'Título',
            ],
        ],
    ],
    'widgets' => [
        'practice_overview' => [
            'heading' => 'Resumen del programa',
            'description' => 'Cobertura, preparación multimedia y ritmo de la biblioteca de prácticas.',
            'stats' => [
                'total_practices' => [
                    'label' => 'Prácticas totales',
                    'description' => 'Elementos de práctica en todos los días y categorías',
                ],
                'enabled_languages' => [
                    'label' => 'Idiomas activos',
                    'description' => 'Idiomas disponibles actualmente en la administración y en las traducciones',
                ],
                'days_covered' => [
                    'label' => 'Días cubiertos',
                    'description' => 'Días únicos con al menos una práctica asignada',
                ],
                'media_ready' => [
                    'label' => 'Multimedia lista',
                    'description' => ':percent% incluyen imagen y video',
                ],
                'average_session' => [
                    'label' => 'Sesión promedio',
                    'description' => 'Duración media de todas las prácticas programadas',
                ],
            ],
        ],
        'practice_volume' => [
            'heading' => 'Volumen de prácticas por día',
            'description' => 'Cómo se distribuyen las prácticas a lo largo del panel de 29 días.',
            'dataset' => 'Prácticas',
        ],
        'practice_duration' => [
            'heading' => 'Duración media de la sesión',
            'description' => 'Promedio de minutos programados para cada día del programa.',
            'dataset' => 'Minutos',
        ],
        'focus_problem_distribution' => [
            'heading' => 'Distribución de problemas centrales',
            'description' => 'Equilibrio de prácticas entre las categorías de problemas centrales.',
            'dataset' => 'Prácticas',
            'empty' => 'Aún no hay prácticas',
        ],
    ],
];
