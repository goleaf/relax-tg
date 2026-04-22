<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExperienceLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'is_enabled',
    ];

    protected $casts = [
        'title' => 'array',
        'is_enabled' => 'boolean',
    ];

    public function getTitle(string $locale = 'en'): string
    {
        return $this->title[$locale] ?? $this->title['en'] ?? '';
    }
}
