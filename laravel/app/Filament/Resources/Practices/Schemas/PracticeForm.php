<?php

namespace App\Filament\Resources\Practices\Schemas;

use App\Filament\Support\LanguageTabsBuilder;
use App\Models\Language;
use App\Models\Practice;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class PracticeForm
{
    /**
     * @param  array<int, string>  $hiddenFields
     */
    public static function configure(Schema $schema, array $hiddenFields = []): Schema
    {
        $categorizationFields = array_values(array_filter([
            static::taxonomySelect(
                field: 'focus_problem_id',
                label: __('admin.resources.practices.fields.focus_problem'),
                relationship: 'focusProblem',
                hidden: in_array('focus_problem_id', $hiddenFields, true),
            ),
            static::taxonomySelect(
                field: 'experience_level_id',
                label: __('admin.resources.practices.fields.experience_level'),
                relationship: 'experienceLevel',
                hidden: in_array('experience_level_id', $hiddenFields, true),
            ),
            static::taxonomySelect(
                field: 'module_choice_id',
                label: __('admin.resources.practices.fields.module_choice'),
                relationship: 'moduleChoice',
                hidden: in_array('module_choice_id', $hiddenFields, true),
            ),
            static::taxonomySelect(
                field: 'meditation_type_id',
                label: __('admin.resources.practices.fields.meditation_type'),
                relationship: 'meditationType',
                hidden: in_array('meditation_type_id', $hiddenFields, true),
            ),
        ], fn (mixed $component): bool => $component !== null));

        return $schema
            ->components([
                Section::make(__('admin.resources.practices.sections.general_and_categorization'))
                    ->schema([
                        Select::make('day')
                            ->label(__('admin.resources.practices.fields.day'))
                            ->options(fn (): array => Practice::dayOptionsWithCounts())
                            ->required()
                            ->default(fn () => data_get(request()->query('filters', []), 'day.value')),
                        Toggle::make('is_active')
                            ->label(__('admin.resources.practices.fields.is_active'))
                            ->default(true),
                        ...$categorizationFields,
                    ])->columns(2),

                Section::make(__('admin.resources.practices.sections.media'))
                    ->schema([
                        TextInput::make('duration')
                            ->label(__('admin.resources.practices.fields.duration'))
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->columnSpanFull(),
                        static::imageUpload(),
                        static::videoUpload(),
                    ])->columns(1),

                Section::make(__('admin.resources.practices.sections.translations'))
                    ->schema([
                        LanguageTabsBuilder::make(function (Language $language) {
                            return [
                                TextInput::make("title.{$language->code}")
                                    ->label(__('admin.resources.practices.fields.title'))
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Textarea::make("description.{$language->code}")
                                    ->label(__('admin.resources.practices.fields.description'))
                                    ->rows(6)
                                    ->columnSpanFull(),
                            ];
                        }),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function taxonomySelect(
        string $field,
        string $label,
        string $relationship,
        bool $hidden = false,
    ): ?Select {
        if ($hidden) {
            return null;
        }

        return Select::make($field)
            ->label($label)
            ->relationship(
                $relationship,
                'id',
                fn (Builder $query) => $query
                    ->forFilamentOptions()
                    ->withCount('practices'),
            )
            ->getOptionLabelFromRecordUsing(
                fn ($record): string => Practice::formatCountedLabel(
                    $record->getTitle(app()->getLocale()),
                    (int) $record->practices_count,
                ),
            )
            ->searchable()
            ->preload()
            ->required()
            ->default(fn () => data_get(request()->query('filters', []), "{$field}.value"));
    }

    private static function imageUpload(): FileUpload
    {
        return FileUpload::make('image_path')
            ->label(__('admin.resources.practices.fields.image'))
            ->disk(Practice::mediaDisk())
            ->directory(Practice::imageDirectory())
            ->visibility('public')
            ->image()
            ->imageEditor()
            ->imagePreviewHeight('240')
            ->panelLayout('integrated')
            ->openable()
            ->downloadable()
            ->maxSize(5120)
            ->deleteUploadedFileUsing(fn (string $file): bool => Storage::disk(Practice::mediaDisk())->delete($file))
            ->preventFilePathTampering()
            ->columnSpanFull();
    }

    private static function videoUpload(): FileUpload
    {
        return FileUpload::make('video_path')
            ->label(__('admin.resources.practices.fields.video'))
            ->disk(Practice::mediaDisk())
            ->directory(Practice::videoDirectory())
            ->visibility('public')
            ->acceptedFileTypes([
                'video/mp4',
                'video/quicktime',
                'video/webm',
            ])
            ->panelLayout('integrated')
            ->openable()
            ->downloadable()
            ->maxSize(10240)
            ->deleteUploadedFileUsing(fn (string $file): bool => Storage::disk(Practice::mediaDisk())->delete($file))
            ->preventFilePathTampering()
            ->columnSpanFull();
    }
}
