<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MeditationTypeResource\Pages;
use App\Filament\Support\LanguageTabsBuilder;
use App\Models\Language;
use App\Models\MeditationType;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class MeditationTypeResource extends Resource
{
    protected static ?string $model = MeditationType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'Categories';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        if ($record === null) {
            return static::getModelLabel();
        }

        return $record->getTitle(app()->getLocale());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General')
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Toggle::make('is_enabled')
                            ->label('Enabled')
                            ->default(true),
                    ]),
                Section::make('Translations')
                    ->schema([
                        LanguageTabsBuilder::make(function (Language $language) {
                            return [
                                TextInput::make("title.{$language->code}")
                                    ->label('Title')
                                    ->required($language->code === 'en')
                                    ->maxLength(255),
                            ];
                        }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $locale = app()->getLocale();

        return $table
            ->columns([
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make("title.{$locale}")
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_enabled')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMeditationTypes::route('/'),
            'create' => Pages\CreateMeditationType::route('/create'),
            'edit' => Pages\EditMeditationType::route('/{record}/edit'),
        ];
    }
}
