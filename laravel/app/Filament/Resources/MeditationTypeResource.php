<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MeditationTypeResource\Pages;
use App\Filament\Resources\MeditationTypeResource\RelationManagers\PracticesRelationManager;
use App\Filament\Support\LanguageTabsBuilder;
use App\Models\Language;
use App\Models\MeditationType;
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
 * @extends resource<MeditationType>
 */
class MeditationTypeResource extends Resource
{
    protected static ?string $model = MeditationType::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    public static function getModelLabel(): string
    {
        return __('admin.resources.meditation_types.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.meditation_types.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.meditation_types.navigation');
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
                Section::make(__('admin.resources.meditation_types.sections.translations'))
                    ->icon(Heroicon::OutlinedLanguage)
                    ->schema([
                        LanguageTabsBuilder::make(function (Language $language) {
                            return [
                                TextInput::make("title.{$language->code}")
                                    ->label(__('admin.resources.meditation_types.fields.title'))
                                    ->prefixIcon(Heroicon::OutlinedSparkles)
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
                TextColumn::make(MeditationType::titleAttribute())
                    ->label(__('admin.resources.meditation_types.fields.title'))
                    ->icon(Heroicon::OutlinedSparkles)
                    ->iconColor('primary')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn (MeditationType $record): bool => $record->isInUse()),
            ])
            ->toolbarActions([
                //
            ]);
    }

    /**
     * @return Builder<MeditationType>
     */
    public static function getEloquentQuery(): Builder
    {
        return MeditationType::query()
            ->withPracticeUsage()
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
            'index' => Pages\ListMeditationTypes::route('/'),
            'create' => Pages\CreateMeditationType::route('/create'),
            'edit' => Pages\EditMeditationType::route('/{record}/edit'),
        ];
    }
}
