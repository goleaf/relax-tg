<?php

namespace App\Filament\Resources\Practices\Schemas;

use App\Enums\ExperienceLevel;
use App\Enums\FocusProblem;
use App\Enums\MeditationType;
use App\Enums\ModuleChoice;
use App\Filament\Support\LanguageTabsBuilder;
use App\Models\Language;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PracticeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General')
                    ->schema([
                        Select::make('day')
                            ->options(array_combine(range(1, 29), array_map(fn ($i) => "{$i} Day", range(1, 29))))
                            ->required()
                            ->default(fn () => request()->query('day')),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])->columns(2),

                Section::make('Categorization')
                    ->schema([
                        Select::make('focus_problem')
                            ->options(FocusProblem::class)
                            ->required(),
                        Select::make('experience_level')
                            ->options(ExperienceLevel::class)
                            ->required(),
                        Select::make('module_choice')
                            ->options(ModuleChoice::class)
                            ->required(),
                        Select::make('meditation_type')
                            ->options(MeditationType::class)
                            ->required(),
                    ])->columns(2),

                Section::make('Media & Duration')
                    ->schema([
                        TextInput::make('duration')
                            ->label('Duration (seconds)')
                            ->numeric()
                            ->required()
                            ->minValue(0),
                        TextInput::make('image_url')
                            ->label('Image URL')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('video_url')
                            ->label('Video URL')
                            ->url()
                            ->maxLength(255),
                    ])->columns(3),

                Section::make('Translations')
                    ->schema([
                        LanguageTabsBuilder::make(function (Language $language) {
                            return [
                                TextInput::make("title.{$language->code}")
                                    ->label('Title')
                                    ->required($language->code === 'en')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Textarea::make("description.{$language->code}")
                                    ->label('Description')
                                    ->rows(6)
                                    ->columnSpanFull(),
                            ];
                        }),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
