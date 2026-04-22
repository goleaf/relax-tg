<?php

return [
    'welcome' => "Вітаємо. Цей бот підключений до адміністративних даних Relax.\n\nКоманди:\n/start\n/help\n/day 1\n/practice 1",
    'help' => "Доступні команди:\n/start - показати короткий вступ\n/help - показати доступні команди\n/day {number} - показати активні практики за день\n/practice {id} - показати одну практику",
    'unknown_command' => 'Невідома команда. Використайте /help, щоб побачити доступні команди.',
    'invalid_day' => 'День має бути в межах від 1 до 29.',
    'day_empty' => 'Для дня :day не знайдено активних практик.',
    'day_intro' => 'Активні практики для дня :day:',
    'practice_missing' => 'Практику № :id не знайдено.',
    'api' => [
        'token_not_configured' => 'Токен API Telegram не налаштовано.',
    ],
    'labels' => [
        'day' => 'День: :value',
        'duration' => 'Тривалість: :value',
        'focus_problem' => 'Ключова проблема: :value',
        'experience_level' => 'Рівень досвіду: :value',
        'module_choice' => 'Вибір модуля: :value',
        'meditation_type' => 'Тип медитації: :value',
    ],
];
