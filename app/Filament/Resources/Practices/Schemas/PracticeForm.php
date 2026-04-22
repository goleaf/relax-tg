<?php

namespace App\Filament\Resources\Practices\Schemas;

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
                        Select::make('focus_problem_id')
                            ->label('Focus Problem')
                            ->relationship('focusProblem', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTitle(app()->getLocale()))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn () => request()->query('focus_problem_id')),
                        Select::make('experience_level_id')
                            ->label('Experience Level')
                            ->relationship('experienceLevel', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTitle(app()->getLocale()))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn () => request()->query('experience_level_id')),
                        Select::make('module_choice_id')
                            ->label('Module Choice')
                            ->relationship('moduleChoice', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTitle(app()->getLocale()))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn () => request()->query('module_choice_id')),
                        Select::make('meditation_type_id')
                            ->label('Meditation Type')
                            ->relationship('meditationType', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTitle(app()->getLocale()))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn () => request()->query('meditation_type_id')),
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
