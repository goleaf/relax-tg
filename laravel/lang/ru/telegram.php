<?php

return [
    'welcome' => "Добро пожаловать. Этот бот подключен к данным админ-панели Relax.\n\nКоманды:\n/start\n/help\n/day 1\n/practice 1",
    'help' => "Доступные команды:\n/start - короткое приветствие\n/help - список команд\n/day {number} - активные практики за день\n/practice {id} - показать одну практику",
    'unknown_command' => "Неизвестная команда.\nИспользуйте /help, чтобы посмотреть доступные команды.",
    'invalid_day' => 'День должен быть в диапазоне от 1 до 29.',
    'day_empty' => 'Для дня :day не найдено активных практик.',
    'day_intro' => 'Активные практики для дня :day:',
    'practice_missing' => 'Практика #:id не найдена.',
    'api' => [
        'token_not_configured' => 'Токен API Telegram не настроен.',
    ],
    'labels' => [
        'day' => 'День: :value',
        'duration' => 'Длительность: :value',
        'focus_problem' => 'Фокус-проблема: :value',
        'experience_level' => 'Уровень подготовки: :value',
        'module_choice' => 'Модуль: :value',
        'meditation_type' => 'Тип медитации: :value',
    ],
];
