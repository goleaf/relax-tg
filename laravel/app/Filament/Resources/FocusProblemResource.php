<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FocusProblemResource\Pages;
use App\Filament\Resources\FocusProblemResource\RelationManagers\PracticesRelationManager;
use App\Filament\Support\LanguageTabsBuilder;
use App\Models\FocusProblem;
use App\Models\Language;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends resource<FocusProblem>
 */
class FocusProblemResource extends Resource
{
    protected static ?string $model = FocusProblem::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function getModelLabel(): string
    {
        return __('admin.resources.focus_problems.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.focus_problems.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.focus_problems.navigation');
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
                Section::make(__('admin.resources.focus_problems.sections.translations'))
                    ->icon(Heroicon::OutlinedLanguage)
                    ->schema([
                        LanguageTabsBuilder::make(function (Language $language) {
                            return [
                                TextInput::make("title.{$language->code}")
                                    ->label(__('admin.resources.focus_problems.fields.title'))
                                    ->prefixIcon(Heroicon::OutlinedTag)
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
                TextColumn::make(FocusProblem::titleAttribute())
                    ->label(__('admin.resources.focus_problems.fields.title'))
                    ->icon(Heroicon::OutlinedTag)
                    ->iconColor('warning')
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

    /**
     * @return Builder<FocusProblem>
     */
    public static function getEloquentQuery(): Builder
    {
        return FocusProblem::query()
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
            'index' => Pages\ListFocusProblems::route('/'),
            'create' => Pages\CreateFocusProblem::route('/create'),
            'edit' => Pages\EditFocusProblem::route('/{record}/edit'),
        ];
    }
}
