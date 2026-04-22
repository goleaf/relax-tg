<?php

return [
    'navigation_groups' => [
        'content' => 'Контент',
        'categories' => 'Категории',
        'daily_practices' => 'Ежедневные практики',
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
                'general' => 'Общее',
                'categorization' => 'Категоризация',
                'media' => 'Медиа и длительность',
                'translations' => 'Переводы',
            ],
            'fields' => [
                'day' => 'День',
                'is_active' => 'Активно',
                'focus_problem' => 'Проблема фокуса',
                'experience_level' => 'Уровень опыта',
                'module_choice' => 'Выбор модуля',
                'meditation_type' => 'Тип медитации',
                'duration' => 'Длительность (секунды)',
                'image' => 'Изображение',
                'video' => 'Видео',
                'title' => 'Название',
                'description' => 'Описание',
            ],
            'short_labels' => [
                'focus_problem' => 'Фокус',
                'experience_level' => 'Уровень',
                'module_choice' => 'Модуль',
                'meditation_type' => 'Тип',
            ],
            'values' => [
                'day' => ':day день',
            ],
            'filters' => [
                'indicator' => ':label: :value',
            ],
        ],
        'languages' => [
            'model' => 'язык',
            'plural' => 'языки',
            'navigation' => 'Языки',
            'sections' => [
                'details' => 'Данные языка',
            ],
            'fields' => [
                'code' => 'Код',
                'name' => 'Название',
                'is_enabled' => 'Включен',
            ],
            'tabs' => [
                'all' => 'Все языки',
                'enabled' => 'Включенные',
                'disabled' => 'Выключенные',
            ],
        ],
        'focus_problems' => [
            'model' => 'проблема фокуса',
            'plural' => 'проблемы фокуса',
            'navigation' => 'Проблемы фокуса',
            'sections' => [
                'translations' => 'Переводы',
            ],
            'fields' => [
                'title' => 'Название',
            ],
        ],
        'experience_levels' => [
            'model' => 'уровень опыта',
            'plural' => 'уровни опыта',
            'navigation' => 'Уровни опыта',
            'sections' => [
                'translations' => 'Переводы',
            ],
            'fields' => [
                'title' => 'Название',
            ],
        ],
        'module_choices' => [
            'model' => 'выбор модуля',
            'plural' => 'варианты модулей',
            'navigation' => 'Варианты модулей',
            'sections' => [
                'translations' => 'Переводы',
            ],
            'fields' => [
                'title' => 'Название',
            ],
        ],
        'meditation_types' => [
            'model' => 'тип медитации',
            'plural' => 'типы медитации',
            'navigation' => 'Типы медитации',
            'sections' => [
                'translations' => 'Переводы',
            ],
            'fields' => [
                'title' => 'Название',
            ],
        ],
    ],
    'widgets' => [
        'practice_overview' => [
            'heading' => 'Сводка программы',
            'description' => 'Покрытие, готовность медиа и темп по всей библиотеке практик.',
            'stats' => [
                'total_practices' => [
                    'label' => 'Всего практик',
                    'description' => 'Элементы практик по всем дням и категориям',
                ],
                'enabled_languages' => [
                    'label' => 'Включенные языки',
                    'description' => 'Языки, доступные в админке и переводах',
                ],
                'days_covered' => [
                    'label' => 'Покрытые дни',
                    'description' => 'Уникальные дни, в которых есть хотя бы одна практика',
                ],
                'media_ready' => [
                    'label' => 'Медиа готовы',
                    'description' => ':percent% содержат и изображение, и видео',
                ],
                'average_session' => [
                    'label' => 'Средняя сессия',
                    'description' => 'Средняя длительность по всем запланированным практикам',
                ],
            ],
        ],
        'practice_volume' => [
            'heading' => 'Количество практик по дням',
            'description' => 'Как практики распределены по 29-дневной программе.',
            'dataset' => 'Практики',
        ],
        'practice_duration' => [
            'heading' => 'Средняя длительность сессии',
            'description' => 'Среднее количество минут, запланированных на каждый день программы.',
            'dataset' => 'Минуты',
        ],
        'focus_problem_distribution' => [
            'heading' => 'Распределение по фокусу',
            'description' => 'Баланс практик по категориям проблемы фокуса.',
            'dataset' => 'Практики',
            'empty' => 'Практик пока нет',
        ],
    ],
];
