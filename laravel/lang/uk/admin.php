<?php

return [
    'navigation_groups' => [
        'content' => 'Вміст',
        'categories' => 'Категорії',
        'daily_practices' => 'Щоденні практики',
    ],
    'relation_managers' => [
        'practices' => 'Практики',
    ],
    'resources' => [
        'practices' => [
            'model' => 'практика',
            'plural' => 'практики',
            'navigation' => 'Практики',
            'sections' => [
                'general' => 'Загальне',
                'categorization' => 'Категоризація',
                'general_and_categorization' => 'Загальне та категоризація',
                'media' => 'Медіа та тривалість',
                'translations' => 'Переклади',
            ],
            'fields' => [
                'day' => 'День',
                'is_active' => 'Активна',
                'focus_problem' => 'Ключова проблема',
                'experience_level' => 'Рівень досвіду',
                'module_choice' => 'Вибір модуля',
                'meditation_type' => 'Тип медитації',
                'duration' => 'Тривалість (секунди)',
                'image' => 'Зображення',
                'video' => 'Відеофайл',
                'title' => 'Назва',
                'description' => 'Опис',
            ],
            'short_labels' => [
                'focus_problem' => 'Фокус',
                'experience_level' => 'Рівень',
                'module_choice' => 'Модуль',
                'meditation_type' => 'Тип',
            ],
            'values' => [
                'day' => 'День :day',
            ],
            'filters' => [
                'indicator' => ':label: :value',
            ],
        ],
        'languages' => [
            'model' => 'мова',
            'plural' => 'мови',
            'navigation' => 'Мови',
            'sections' => [
                'details' => 'Деталі мови',
            ],
            'fields' => [
                'code' => 'Код',
                'name' => 'Назва',
                'native_name' => 'Рідна назва',
                'is_enabled' => 'Увімкнено',
            ],
            'tabs' => [
                'all' => 'Усі мови',
                'enabled' => 'Увімкнені',
                'disabled' => 'Вимкнені',
            ],
        ],
        'focus_problems' => [
            'model' => 'ключова проблема',
            'plural' => 'ключові проблеми',
            'navigation' => 'Ключові проблеми',
            'sections' => [
                'translations' => 'Переклади',
            ],
            'fields' => [
                'title' => 'Назва',
            ],
        ],
        'experience_levels' => [
            'model' => 'рівень досвіду',
            'plural' => 'рівні досвіду',
            'navigation' => 'Рівні досвіду',
            'sections' => [
                'translations' => 'Переклади',
            ],
            'fields' => [
                'title' => 'Назва',
            ],
        ],
        'module_choices' => [
            'model' => 'вибір модуля',
            'plural' => 'варіанти модуля',
            'navigation' => 'Вибір модуля',
            'sections' => [
                'translations' => 'Переклади',
            ],
            'fields' => [
                'title' => 'Назва',
            ],
        ],
        'meditation_types' => [
            'model' => 'тип медитації',
            'plural' => 'типи медитації',
            'navigation' => 'Типи медитації',
            'sections' => [
                'translations' => 'Переклади',
            ],
            'fields' => [
                'title' => 'Назва',
            ],
        ],
    ],
    'widgets' => [
        'practice_overview' => [
            'heading' => 'Огляд програми',
            'description' => 'Покриття, готовність медіа та ритм бібліотеки практик.',
            'stats' => [
                'total_practices' => [
                    'label' => 'Усього практик',
                    'description' => 'Елементи практик для всіх днів і категорій',
                ],
                'enabled_languages' => [
                    'label' => 'Увімкнені мови',
                    'description' => 'Мови, які зараз доступні в адмінці та перекладах',
                ],
                'days_covered' => [
                    'label' => 'Охоплені дні',
                    'description' => 'Унікальні дні, для яких призначено принаймні одну практику',
                ],
                'media_ready' => [
                    'label' => 'Медіа готові',
                    'description' => ':percent% містять і зображення, і відео',
                ],
                'average_session' => [
                    'label' => 'Середня сесія',
                    'description' => 'Середня тривалість усіх запланованих практик',
                ],
            ],
        ],
        'practice_volume' => [
            'heading' => 'Кількість практик за днем',
            'description' => 'Як практики розподілені на 29-денній панелі.',
            'dataset' => 'Практики',
        ],
        'practice_duration' => [
            'heading' => 'Середня тривалість сесії',
            'description' => 'Середня кількість хвилин, запланованих на кожен день програми.',
            'dataset' => 'Хвилини',
        ],
        'focus_problem_distribution' => [
            'heading' => 'Розподіл ключових проблем',
            'description' => 'Баланс практик між категоріями ключових проблем.',
            'dataset' => 'Практики',
            'empty' => 'Практик ще немає',
        ],
    ],
];
