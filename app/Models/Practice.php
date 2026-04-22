<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $day
 * @property int|null $focus_problem_id
 * @property int|null $experience_level_id
 * @property int|null $module_choice_id
 * @property int|null $meditation_type_id
 * @property int $duration
 * @property string|null $image_path
 * @property string|null $video_path
 * @property bool $is_active
 * @property array<string, string> $title
 * @property array<string, string>|null $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read FocusProblem|null $focusProblem
 * @property-read ExperienceLevel|null $experienceLevel
 * @property-read ModuleChoice|null $moduleChoice
 * @property-read MeditationType|null $meditationType
 */
class Practice extends Model
{
    use HasFactory;

    private const DAY_TOTAL = 29;

    private const MEDIA_DISK = 'public';

    private const IMAGE_DIRECTORY = 'practices/images';

    private const VIDEO_DIRECTORY = 'practices/videos';

    /**
     * @var array<int, string>
     */
    private const MEDIA_ATTRIBUTES = [
        'image_path',
        'video_path',
    ];

    /**
     * @var array<string, array{model: class-string<FocusProblem|ExperienceLevel|ModuleChoice|MeditationType>, label_key: string}>
     */
    private const RELATION_FILTERS = [
        'focus_problem_id' => ['model' => FocusProblem::class, 'label_key' => 'focus_problem'],
        'experience_level_id' => ['model' => ExperienceLevel::class, 'label_key' => 'experience_level'],
        'module_choice_id' => ['model' => ModuleChoice::class, 'label_key' => 'module_choice'],
        'meditation_type_id' => ['model' => MeditationType::class, 'label_key' => 'meditation_type'],
    ];

    /**
     * @var array<int, string>
     */
    private array $mediaPathsPendingDeletion = [];

    protected $fillable = [
        'day',
        'focus_problem_id',
        'experience_level_id',
        'module_choice_id',
        'meditation_type_id',
        'duration',
        'image_path',
        'video_path',
        'is_active',
        'title',
        'description',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    protected $casts = [
        'day' => 'integer',
        'duration' => 'integer',
        'is_active' => 'boolean',
        'title' => 'array',
        'description' => 'array',
    ];

    protected static function booted(): void
    {
        static::updating(function (Practice $practice): void {
            $practice->captureMediaPathsPendingDeletion();
        });

        static::updated(function (Practice $practice): void {
            $practice->deleteMediaFiles($practice->mediaPathsPendingDeletion);
            $practice->mediaPathsPendingDeletion = [];
        });

        static::deleted(function (Practice $practice): void {
            $practice->deleteMediaFiles([
                $practice->image_path,
                $practice->video_path,
            ]);
        });
    }

    public function focusProblem(): BelongsTo
    {
        return $this->belongsTo(FocusProblem::class);
    }

    public function experienceLevel(): BelongsTo
    {
        return $this->belongsTo(ExperienceLevel::class);
    }

    public function moduleChoice(): BelongsTo
    {
        return $this->belongsTo(ModuleChoice::class);
    }

    public function meditationType(): BelongsTo
    {
        return $this->belongsTo(MeditationType::class);
    }

    public static function translatedAttribute(string $attribute, ?string $locale = null): string
    {
        return $attribute.'.'.($locale ?? app()->getLocale());
    }

    /**
     * @return array<int, string>
     */
    public static function dayOptions(): array
    {
        return collect(range(1, self::DAY_TOTAL))
            ->mapWithKeys(fn (int $day): array => [$day => static::formatDay($day)])
            ->all();
    }

    public static function formatDay(int $day): string
    {
        return __('admin.resources.practices.values.day', ['day' => $day]);
    }

    public static function formatDuration(int $duration): string
    {
        return floor($duration / 60).':'.str_pad((string) ($duration % 60), 2, '0', STR_PAD_LEFT);
    }

    /**
     * @return array<int, string>
     */
    public static function dayOptionsWithCounts(): array
    {
        return collect(static::getNavigationTree())
            ->mapWithKeys(fn (array $dayData): array => [
                $dayData['day'] => static::formatCountedLabel(
                    static::formatDay($dayData['day']),
                    $dayData['count'],
                ),
            ])
            ->all();
    }

    public static function mediaDisk(): string
    {
        return self::MEDIA_DISK;
    }

    public static function imageDirectory(): string
    {
        return self::IMAGE_DIRECTORY;
    }

    public static function videoDirectory(): string
    {
        return self::VIDEO_DIRECTORY;
    }

    public function getTitle(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $this->title[$locale]
            ?? $this->title[config('app.fallback_locale', 'en')]
            ?? collect($this->title)
                ->first(fn (?string $value): bool => filled($value), '')
            ?? '';
    }

    public function getDescription(?string $locale = null): ?string
    {
        if (empty($this->description)) {
            return null;
        }

        $locale ??= app()->getLocale();

        return $this->description[$locale]
            ?? $this->description[config('app.fallback_locale', 'en')]
            ?? collect($this->description)
                ->first(fn (?string $value): bool => filled($value));
    }

    public function getImageUrl(): ?string
    {
        return $this->getMediaUrl($this->image_path);
    }

    public function getVideoUrl(): ?string
    {
        return $this->getMediaUrl($this->video_path);
    }

    public static function formatCountedLabel(string $label, int $count): string
    {
        return "{$label} ({$count})";
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderBy('id', 'desc');
    }

    public function scopeForDay(Builder $query, ?int $day): Builder
    {
        return $query->when(filled($day), fn (Builder $query) => $query->where('day', $day));
    }

    public function scopeForFocusProblem(Builder $query, ?int $focusProblemId): Builder
    {
        return $query->when(
            filled($focusProblemId),
            fn (Builder $query) => $query->where('focus_problem_id', $focusProblemId),
        );
    }

    public function scopeForExperienceLevel(Builder $query, ?int $experienceLevelId): Builder
    {
        return $query->when(
            filled($experienceLevelId),
            fn (Builder $query) => $query->where('experience_level_id', $experienceLevelId),
        );
    }

    public function scopeForModuleChoice(Builder $query, ?int $moduleChoiceId): Builder
    {
        return $query->when(
            filled($moduleChoiceId),
            fn (Builder $query) => $query->where('module_choice_id', $moduleChoiceId),
        );
    }

    public function scopeForMeditationType(Builder $query, ?int $meditationTypeId): Builder
    {
        return $query->when(
            filled($meditationTypeId),
            fn (Builder $query) => $query->where('meditation_type_id', $meditationTypeId),
        );
    }

    public function scopeSelectResourceColumns(Builder $query): Builder
    {
        return $query->select([
            'id',
            'day',
            'focus_problem_id',
            'experience_level_id',
            'module_choice_id',
            'meditation_type_id',
            'duration',
            'image_path',
            'video_path',
            'is_active',
            'title',
            'description',
            'created_at',
            'updated_at',
        ]);
    }

    public function scopeWithTaxonomyTitles(Builder $query): Builder
    {
        return $query->with([
            'focusProblem:id,title',
            'experienceLevel:id,title',
            'moduleChoice:id,title',
            'meditationType:id,title',
        ]);
    }

    public function scopeForResourceIndex(Builder $query): Builder
    {
        return $query
            ->selectResourceColumns()
            ->withTaxonomyTitles();
    }

    public function scopeSelectNavigationCounts(Builder $query): Builder
    {
        return $query
            ->select('day')
            ->selectRaw('count(*) as total')
            ->groupBy('day');
    }

    /**
     * @return array<int, array{day: int, count: int}>
     */
    public static function getNavigationTree(): array
    {
        $counts = static::query()
            ->selectNavigationCounts()
            ->pluck('total', 'day');

        return collect(range(1, self::DAY_TOTAL))
            ->map(function (int $day) use ($counts): array {
                return [
                    'day' => $day,
                    'count' => (int) ($counts[$day] ?? 0),
                ];
            })
            ->all();
    }

    public static function getListTitle(array $filters, string $locale, int $count): string
    {
        $parts = [];

        if ($day = data_get($filters, 'day.value')) {
            $parts[] = static::formatDay((int) $day);
        }

        foreach (self::RELATION_FILTERS as $field => $filter) {
            $label = static::relationFilterIndicator(
                $field,
                data_get($filters, "{$field}.value"),
                $locale,
            );

            if ($label !== null) {
                $parts[] = $label;
            }
        }

        $baseTitle = $parts === [] ? __('admin.resources.practices.navigation') : implode(' / ', $parts);

        return "{$baseTitle} ({$count})";
    }

    public static function relationFilterIndicator(
        string $field,
        int|string|null $value,
        string $locale,
        ?string $prefix = null,
    ): ?string {
        $title = static::relationFilterTitle($field, $value, $locale);

        if ($title === null) {
            return null;
        }

        return __('admin.resources.practices.filters.indicator', [
            'label' => $prefix ?? static::relationFilterLabel($field),
            'value' => $title,
        ]);
    }

    public static function relationFilterTitle(
        string $field,
        int|string|null $value,
        string $locale,
    ): ?string {
        $filter = self::RELATION_FILTERS[$field] ?? null;

        if (($filter === null) || blank($value)) {
            return null;
        }

        /** @var FocusProblem|ExperienceLevel|ModuleChoice|MeditationType|null $record */
        $record = $filter['model']::query()
            ->forFilamentOptions()
            ->find($value);

        if ($record === null) {
            return null;
        }

        return $record->getTitle($locale);
    }

    public static function relationFilterLabel(string $field): string
    {
        $filter = self::RELATION_FILTERS[$field] ?? null;

        if ($filter === null) {
            return $field;
        }

        return __("admin.resources.practices.short_labels.{$filter['label_key']}");
    }

    private function captureMediaPathsPendingDeletion(): void
    {
        $paths = [];

        foreach (self::MEDIA_ATTRIBUTES as $attribute) {
            if (! $this->isDirty($attribute)) {
                continue;
            }

            $originalPath = $this->getOriginal($attribute);

            if (filled($originalPath)) {
                $paths[] = $originalPath;
            }
        }

        $this->mediaPathsPendingDeletion = array_values(array_unique($paths));
    }

    private function deleteMediaFiles(array $paths): void
    {
        collect($paths)
            ->filter(fn (?string $path): bool => filled($path))
            ->unique()
            ->each(fn (string $path) => Storage::disk(self::MEDIA_DISK)->delete($path));
    }

    private function getMediaUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return Storage::disk(self::MEDIA_DISK)->url($path);
    }
}
