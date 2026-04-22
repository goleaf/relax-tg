<?php

return [
    'navigation_groups' => [
        'content' => 'Content',
        'categories' => 'Categories',
        'daily_practices' => 'Daily Practices',
    ],
    'relation_managers' => [
        'practices' => 'Practices',
    ],
    'resources' => [
        'practices' => [
            'model' => 'practice',
            'plural' => 'practices',
            'navigation' => 'Practices',
            'sections' => [
                'general' => 'General',
                'categorization' => 'Categorization',
                'general_and_categorization' => 'General and Categorization',
                'media' => 'Media & Duration',
                'translations' => 'Translations',
            ],
            'fields' => [
                'day' => 'Day',
                'is_active' => 'Active',
                'focus_problem' => 'Focus Problem',
                'experience_level' => 'Experience Level',
                'module_choice' => 'Module Choice',
                'meditation_type' => 'Meditation Type',
                'duration' => 'Duration (seconds)',
                'image' => 'Image',
                'video' => 'Video File',
                'title' => 'Title',
                'description' => 'Description',
            ],
            'short_labels' => [
                'focus_problem' => 'Focus',
                'experience_level' => 'Level',
                'module_choice' => 'Module',
                'meditation_type' => 'Type',
            ],
            'values' => [
                'day' => ':day Day',
            ],
            'filters' => [
                'indicator' => ':label: :value',
            ],
        ],
        'languages' => [
            'model' => 'language',
            'plural' => 'languages',
            'navigation' => 'Languages',
            'sections' => [
                'details' => 'Language Details',
            ],
            'fields' => [
                'code' => 'Code',
                'name' => 'Name',
                'native_name' => 'Native Name',
                'is_enabled' => 'Enabled',
            ],
            'tabs' => [
                'all' => 'All Languages',
                'enabled' => 'Enabled',
                'disabled' => 'Disabled',
            ],
        ],
        'focus_problems' => [
            'model' => 'focus problem',
            'plural' => 'focus problems',
            'navigation' => 'Focus Problems',
            'sections' => [
                'translations' => 'Translations',
            ],
            'fields' => [
                'title' => 'Title',
            ],
        ],
        'experience_levels' => [
            'model' => 'experience level',
            'plural' => 'experience levels',
            'navigation' => 'Experience Levels',
            'sections' => [
                'translations' => 'Translations',
            ],
            'fields' => [
                'title' => 'Title',
            ],
        ],
        'module_choices' => [
            'model' => 'module choice',
            'plural' => 'module choices',
            'navigation' => 'Module Choices',
            'sections' => [
                'translations' => 'Translations',
            ],
            'fields' => [
                'title' => 'Title',
            ],
        ],
        'meditation_types' => [
            'model' => 'meditation type',
            'plural' => 'meditation types',
            'navigation' => 'Meditation Types',
            'sections' => [
                'translations' => 'Translations',
            ],
            'fields' => [
                'title' => 'Title',
            ],
        ],
    ],
    'widgets' => [
        'practice_overview' => [
            'heading' => 'Program Snapshot',
            'description' => 'Coverage, media readiness, and pacing across the practice library.',
            'stats' => [
                'total_practices' => [
                    'label' => 'Total practices',
                    'description' => 'Practice items across every day and category combination',
                ],
                'enabled_languages' => [
                    'label' => 'Enabled languages',
                    'description' => 'Languages currently available in the admin and translations',
                ],
                'days_covered' => [
                    'label' => 'Days covered',
                    'description' => 'Unique day slots with at least one practice assigned',
                ],
                'media_ready' => [
                    'label' => 'Media ready',
                    'description' => ':percent% include both image and video',
                ],
                'average_session' => [
                    'label' => 'Average session',
                    'description' => 'Mean duration across all scheduled practices',
                ],
            ],
        ],
        'practice_volume' => [
            'heading' => 'Practice Volume by Day',
            'description' => 'How practice items are distributed across the 29-day dashboard.',
            'dataset' => 'Practices',
        ],
        'practice_duration' => [
            'heading' => 'Average Session Length',
            'description' => 'Average minutes scheduled for each day in the program.',
            'dataset' => 'Minutes',
        ],
        'focus_problem_distribution' => [
            'heading' => 'Focus Problem Mix',
            'description' => 'Balance of practices across the focus problem categories.',
            'dataset' => 'Practices',
            'empty' => 'No practices yet',
        ],
    ],
];
