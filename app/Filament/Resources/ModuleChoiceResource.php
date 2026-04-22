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

    public static function getModelLabel(): string
    {
        return __('admin.resources.module_choices.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.module_choices.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.module_choices.navigation');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('admin.navigation_groups.categories');
    }

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
                Section::make(__('admin.resources.module_choices.sections.translations'))
                    ->schema([
                        LanguageTabsBuilder::make(function (Language $language) {
                            return [
                                TextInput::make("title.{$language->code}")
                                    ->label(__('admin.resources.module_choices.fields.title'))
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
                    ->label(__('admin.resources.module_choices.fields.title'))
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
