<?php

namespace App\Enums;

enum MeditationType: string
{
    case Breath = 'breath';
    case Body = 'body';
    case Observation = 'observation';
    case Movement = 'movement';
    case Pause = 'pause';
    case Space = 'space';
}
