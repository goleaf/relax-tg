<?php

return [
    'welcome' => "Bienvenido. Este bot está conectado a los datos de administración de Relax.\n\nComandos:\n/start\n/help\n/day 1\n/practice 1",
    'help' => "Comandos disponibles:\n/start - mostrar una breve introducción\n/help - mostrar los comandos disponibles\n/day {number} - listar las prácticas activas de un día\n/practice {id} - mostrar una práctica",
    'unknown_command' => 'Comando desconocido. Usa /help para ver los comandos disponibles.',
    'invalid_day' => 'El día debe estar entre 1 y 29.',
    'day_empty' => 'No se encontraron prácticas activas para el día :day.',
    'day_intro' => 'Prácticas activas para el día :day:',
    'practice_missing' => 'No se encontró la práctica n.º :id.',
    'api' => [
        'token_not_configured' => 'El token de la API de Telegram no está configurado.',
    ],
    'labels' => [
        'day' => 'Día: :value',
        'duration' => 'Duración: :value',
        'focus_problem' => 'Problema central: :value',
        'experience_level' => 'Nivel de experiencia: :value',
        'module_choice' => 'Selección de módulo: :value',
        'meditation_type' => 'Tipo de meditación: :value',
    ],
];
