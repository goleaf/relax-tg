<?php

return [
    'welcome' => "Bienvenue. Ce bot est connecté aux données d’administration de Relax.\n\nCommandes :\n/start\n/help\n/day 1\n/practice 1",
    'help' => "Commandes disponibles :\n/start - afficher une courte introduction\n/help - afficher les commandes disponibles\n/day {number} - lister les pratiques actives d’un jour\n/practice {id} - afficher une pratique",
    'unknown_command' => 'Commande inconnue. Utilisez /help pour voir les commandes disponibles.',
    'invalid_day' => 'Le jour doit être compris entre 1 et 29.',
    'day_empty' => 'Aucune pratique active n’a été trouvée pour le jour :day.',
    'day_intro' => 'Pratiques actives pour le jour :day :',
    'practice_missing' => 'La pratique n° :id est introuvable.',
    'api' => [
        'token_not_configured' => 'Le jeton de l\'API Telegram n\'est pas configuré.',
    ],
    'labels' => [
        'day' => 'Jour : :value',
        'duration' => 'Durée : :value',
        'focus_problem' => 'Problème ciblé : :value',
        'experience_level' => 'Niveau d’expérience : :value',
        'module_choice' => 'Choix du module : :value',
        'meditation_type' => 'Type de méditation : :value',
    ],
];
