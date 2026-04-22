<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModuleChoiceResource\Pages;
use App\Filament\Resources\ModuleChoiceResource\RelationManagers\PracticesRelationManager;
use App\Filament\Support\LanguageTabsBuilder;
use App\Models\Language;
use App\Models\ModuleChoice;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ModuleChoiceResource extends Resource
{
    protected static ?string $model = ModuleChoice::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

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
                Section::make('Translations')
                    ->schema([
                        LanguageTabsBuilder::make(function (Language $language) {
                            return [
                                TextInput::make("title.{$language->code}")
                                    ->label('Title')
                                    ->required()
                                    ->maxLength(255),
                            ];
                        }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id')
            ->columns([
                TextColumn::make(ModuleChoice::titleAttribute())
                    ->label('Title')
                    ->searchable()
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->forFilamentIndex();
    }

    public static function getRelations(): array
    {
        return [
            PracticesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListModuleChoices::route('/'),
            'create' => Pages\CreateModuleChoice::route('/create'),
            'edit' => Pages\EditModuleChoice::route('/{record}/edit'),
        ];
    }
}
