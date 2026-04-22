<?php

namespace App\Models;

use Database\Factories\PracticeFactory;
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
    /** @use HasFactory<PracticeFactory> */
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

    /**
     * @return BelongsTo<FocusProblem, $this>
     */
    public function focusProblem(): BelongsTo
    {
        return $this->belongsTo(FocusProblem::class);
    }

    /**
     * @return BelongsTo<ExperienceLevel, $this>
     */
    public function experienceLevel(): BelongsTo
    {
        return $this->belongsTo(ExperienceLevel::class);
    }

    /**
     * @return BelongsTo<ModuleChoice, $this>
     */
    public function moduleChoice(): BelongsTo
    {
        return $this->belongsTo(ModuleChoice::class);
    }

    /**
     * @return BelongsTo<MeditationType, $this>
     */
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
        $fallbackLocale = config('app.fallback_locale', 'en');
        $fallbackLocale = is_string($fallbackLocale) ? $fallbackLocale : 'en';

        if (isset($this->title[$locale]) && filled($this->title[$locale])) {
            return $this->title[$locale];
        }

        if (isset($this->title[$fallbackLocale]) && filled($this->title[$fallbackLocale])) {
            return $this->title[$fallbackLocale];
        }

        foreach ($this->title as $value) {
            if (filled($value)) {
                return $value;
            }
        }

        return '';
    }

    public function getDescription(?string $locale = null): ?string
    {
        if (($this->description === null) || ($this->description === [])) {
            return null;
        }

        $locale ??= app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');
        $fallbackLocale = is_string($fallbackLocale) ? $fallbackLocale : 'en';

        if (isset($this->description[$locale]) && filled($this->description[$locale])) {
            return $this->description[$locale];
        }

        if (isset($this->description[$fallbackLocale]) && filled($this->description[$fallbackLocale])) {
            return $this->description[$fallbackLocale];
        }

        foreach ($this->description as $value) {
            if (filled($value)) {
                return $value;
            }
        }

        return null;
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

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderBy('id', 'desc');
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeOrderedForProgram(Builder $query): Builder
    {
        return $query
            ->orderBy('day')
            ->orderBy('id');
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForDay(Builder $query, ?int $day): Builder
    {
        return $query->when(filled($day), fn (Builder $query) => $query->where('day', $day));
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForFocusProblem(Builder $query, ?int $focusProblemId): Builder
    {
        return $query->when(
            filled($focusProblemId),
            fn (Builder $query) => $query->where('focus_problem_id', $focusProblemId),
        );
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForExperienceLevel(Builder $query, ?int $experienceLevelId): Builder
    {
        return $query->when(
            filled($experienceLevelId),
            fn (Builder $query) => $query->where('experience_level_id', $experienceLevelId),
        );
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForModuleChoice(Builder $query, ?int $moduleChoiceId): Builder
    {
        return $query->when(
            filled($moduleChoiceId),
            fn (Builder $query) => $query->where('module_choice_id', $moduleChoiceId),
        );
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForMeditationType(Builder $query, ?int $meditationTypeId): Builder
    {
        return $query->when(
            filled($meditationTypeId),
            fn (Builder $query) => $query->where('meditation_type_id', $meditationTypeId),
        );
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
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

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeWithTaxonomyTitles(Builder $query): Builder
    {
        return $query->with([
            'focusProblem:id,title',
            'experienceLevel:id,title',
            'moduleChoice:id,title',
            'meditationType:id,title',
        ]);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForResourceIndex(Builder $query): Builder
    {
        return $this->scopeWithTaxonomyTitles(
            $this->scopeSelectResourceColumns($query),
        );
    }

    /**
     * @param  array{
     *     day?: int|string|null,
     *     focus_problem_id?: int|string|null,
     *     experience_level_id?: int|string|null,
     *     module_choice_id?: int|string|null,
     *     meditation_type_id?: int|string|null
     * }  $filters
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForTelegramDelivery(Builder $query, array $filters = [], bool $activeOnly = true): Builder
    {
        $query = $this->scopeWithTaxonomyTitles(
            $this->scopeSelectResourceColumns($query),
        );

        if ($activeOnly) {
            $query = $this->scopeActive($query);
        }

        $query = $this->scopeForDay($query, self::nullableInt($filters['day'] ?? null));
        $query = $this->scopeForFocusProblem($query, self::nullableInt($filters['focus_problem_id'] ?? null));
        $query = $this->scopeForExperienceLevel($query, self::nullableInt($filters['experience_level_id'] ?? null));
        $query = $this->scopeForModuleChoice($query, self::nullableInt($filters['module_choice_id'] ?? null));
        $query = $this->scopeForMeditationType($query, self::nullableInt($filters['meditation_type_id'] ?? null));

        return $this->scopeOrderedForProgram($query);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeSelectDayCounts(Builder $query): Builder
    {
        return $query
            ->select('day')
            ->selectRaw('count(*) as total')
            ->groupBy('day');
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeSelectAverageDurationMinutesByDay(Builder $query): Builder
    {
        return $query
            ->select('day')
            ->selectRaw('round(avg(duration) / 60.0, 1) as average_minutes')
            ->groupBy('day');
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeSelectMediaReadyCountsByDay(Builder $query): Builder
    {
        return $query
            ->select('day')
            ->whereNotNull('image_path')
            ->whereNotNull('video_path')
            ->selectRaw('count(*) as total')
            ->groupBy('day');
    }

    /**
     * @return array<int, int>
     */
    public static function dayCounts(): array
    {
        return once(fn (): array => static::query()
            ->selectDayCounts()
            ->pluck('total', 'day')
            ->mapWithKeys(fn (mixed $count, mixed $day): array => self::integerMapEntry($day, $count))
            ->all());
    }

    /**
     * @return array<int, float>
     */
    public static function averageDurationMinutesByDay(): array
    {
        return once(fn (): array => static::query()
            ->selectAverageDurationMinutesByDay()
            ->pluck('average_minutes', 'day')
            ->mapWithKeys(fn (mixed $average, mixed $day): array => self::floatMapEntry($day, $average))
            ->all());
    }

    /**
     * @return array<int, int>
     */
    public static function mediaReadyCountsByDay(): array
    {
        return once(fn (): array => static::query()
            ->selectMediaReadyCountsByDay()
            ->pluck('total', 'day')
            ->mapWithKeys(fn (mixed $count, mixed $day): array => self::integerMapEntry($day, $count))
            ->all());
    }

    /**
     * @return array<int, array{day: int, count: int}>
     */
    public static function getNavigationTree(): array
    {
        $counts = static::dayCounts();

        return collect(range(1, self::DAY_TOTAL))
            ->map(function (int $day) use ($counts): array {
                return [
                    'day' => $day,
                    'count' => $counts[$day] ?? 0,
                ];
            })
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public static function getListTitle(array $filters, string $locale, int $count): string
    {
        $parts = [];

        $day = self::nullableInt(data_get($filters, 'day.value'));

        if ($day !== null) {
            $parts[] = static::formatDay($day);
        }

        foreach (self::RELATION_FILTERS as $field => $filter) {
            $label = static::relationFilterIndicator(
                $field,
                self::nullableIntOrString(data_get($filters, "{$field}.value")),
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
        if (blank($value)) {
            return null;
        }

        return self::relationFilterTitles($field, $locale)[(int) $value] ?? null;
    }

    public static function relationFilterLabel(string $field): string
    {
        $filter = self::RELATION_FILTERS[$field] ?? null;

        if ($filter === null) {
            return $field;
        }

        return __("admin.resources.practices.short_labels.{$filter['label_key']}");
    }

    /**
     * @return array<int, string>
     */
    private static function relationFilterTitles(string $field, string $locale): array
    {
        /** @var array<string, array<int, string>> $titles */
        static $titles = [];

        $cacheKey = "{$field}:{$locale}";

        if (array_key_exists($cacheKey, $titles)) {
            return $titles[$cacheKey];
        }

        $filter = self::RELATION_FILTERS[$field] ?? null;

        if ($filter === null) {
            return $titles[$cacheKey] = [];
        }

        /** @var class-string<FocusProblem|ExperienceLevel|ModuleChoice|MeditationType> $modelClass */
        $modelClass = $filter['model'];

        return $titles[$cacheKey] = $modelClass::query()
            ->forFilamentOptions()
            ->get()
            ->mapWithKeys(fn (FocusProblem|ExperienceLevel|ModuleChoice|MeditationType $record): array => [
                $record->id => $record->getTitle($locale),
            ])
            ->all();
    }

    private function captureMediaPathsPendingDeletion(): void
    {
        $paths = [];

        foreach (self::MEDIA_ATTRIBUTES as $attribute) {
            if (! $this->isDirty($attribute)) {
                continue;
            }

            $originalPath = $this->getOriginal($attribute);

            if (is_string($originalPath) && filled($originalPath)) {
                $paths[] = $originalPath;
            }
        }

        $this->mediaPathsPendingDeletion = array_values(array_unique($paths));
    }

    /**
     * @param  array<int, string|null>  $paths
     */
    private function deleteMediaFiles(array $paths): void
    {
        foreach (array_unique($paths) as $path) {
            if (blank($path)) {
                continue;
            }

            Storage::disk(self::MEDIA_DISK)->delete($path);
        }
    }

    private function getMediaUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return Storage::disk(self::MEDIA_DISK)->url($path);
    }

    private static function nullableInt(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }

    private static function nullableIntOrString(mixed $value): int|string|null
    {
        if (is_int($value) || is_string($value)) {
            return blank($value) ? null : $value;
        }

        return null;
    }

    /**
     * @return array<int, int>
     */
    private static function integerMapEntry(mixed $key, mixed $value): array
    {
        if (! is_numeric($key) || ! is_numeric($value)) {
            return [];
        }

        return [
            (int) $key => (int) $value,
        ];
    }

    /**
     * @return array<int, float>
     */
    private static function floatMapEntry(mixed $key, mixed $value): array
    {
        if (! is_numeric($key) || ! is_numeric($value)) {
            return [];
        }

        return [
            (int) $key => (float) $value,
        ];
    }
}
