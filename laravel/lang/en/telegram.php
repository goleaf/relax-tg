<?php

return [
    'welcome' => "Welcome. This bot is connected to the Relax admin data.\n\nCommands:\n/start\n/help\n/day 1\n/practice 1",
    'help' => "Available commands:\n/start - show a short introduction\n/help - show available commands\n/day {number} - list active practices for a day\n/practice {id} - show one practice",
    'unknown_command' => "Unknown command.\nUse /help to see the available commands.",
    'invalid_day' => 'Day must be between 1 and 29.',
    'day_empty' => 'No active practices were found for day :day.',
    'day_intro' => 'Active practices for day :day:',
    'practice_missing' => 'Practice #:id was not found.',
    'api' => [
        'token_not_configured' => 'Telegram API token is not configured.',
    ],
    'labels' => [
        'day' => 'Day: :value',
        'duration' => 'Duration: :value',
        'focus_problem' => 'Focus problem: :value',
        'experience_level' => 'Experience level: :value',
        'module_choice' => 'Module choice: :value',
        'meditation_type' => 'Meditation type: :value',
    ],
];
