<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExperienceLevelResource\Pages;
use App\Filament\Resources\ExperienceLevelResource\RelationManagers\PracticesRelationManager;
use App\Filament\Support\LanguageTabsBuilder;
use App\Models\ExperienceLevel;
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
 * @extends resource<ExperienceLevel>
 */
class ExperienceLevelResource extends Resource
{
    protected static ?string $model = ExperienceLevel::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    public static function getModelLabel(): string
    {
        return __('admin.resources.experience_levels.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.experience_levels.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.experience_levels.navigation');
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
                Section::make(__('admin.resources.experience_levels.sections.translations'))
                    ->icon(Heroicon::OutlinedLanguage)
                    ->schema([
                        LanguageTabsBuilder::make(function (Language $language) {
                            return [
                                TextInput::make("title.{$language->code}")
                                    ->label(__('admin.resources.experience_levels.fields.title'))
                                    ->prefixIcon(Heroicon::OutlinedChartBar)
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
                TextColumn::make(ExperienceLevel::titleAttribute())
                    ->label(__('admin.resources.experience_levels.fields.title'))
                    ->icon(Heroicon::OutlinedChartBar)
                    ->iconColor('info')
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
     * @return Builder<ExperienceLevel>
     */
    public static function getEloquentQuery(): Builder
    {
        return ExperienceLevel::query()
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
            'index' => Pages\ListExperienceLevels::route('/'),
            'create' => Pages\CreateExperienceLevel::route('/create'),
            'edit' => Pages\EditExperienceLevel::route('/{record}/edit'),
        ];
    }
}
