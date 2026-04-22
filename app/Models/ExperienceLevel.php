<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExperienceLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
    ];

    protected $casts = [
        'title' => 'array',
    ];

    public function getTitle(string $locale): string
    {
        return $this->title[$locale] ?? '';
    }
}
