<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Practice extends Model
{
    /** @use HasFactory<\Database\Factories\PracticeFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'json',
            'description' => 'json',
        ];
    }
}
