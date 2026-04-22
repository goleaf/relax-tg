<?php

use App\Enums\ExperienceLevel as ExperienceLevelEnum;
use App\Enums\FocusProblem as FocusProblemEnum;
use App\Enums\MeditationType as MeditationTypeEnum;
use App\Enums\ModuleChoice as ModuleChoiceEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->enum('focus_problem', FocusProblemEnum::values())->nullable()->after('day');
            $table->enum('experience_level', ExperienceLevelEnum::values())->nullable()->after('focus_problem');
            $table->enum('module_choice', ModuleChoiceEnum::values())->nullable()->after('experience_level');
            $table->enum('meditation_type', MeditationTypeEnum::values())->nullable()->after('module_choice');
        });

        $focusProblemIds = $this->categoryIdsByEnumValue('focus_problems', 'focus_problem');
        $experienceLevelIds = $this->categoryIdsByEnumValue('experience_levels', 'experience_level');
        $moduleChoiceIds = $this->categoryIdsByEnumValue('module_choices', 'module_choice');
        $meditationTypeIds = $this->categoryIdsByEnumValue('meditation_types', 'meditation_type');

        DB::table('practices')
            ->select([
                'id',
                'focus_problem_id',
                'experience_level_id',
                'module_choice_id',
                'meditation_type_id',
            ])
            ->orderBy('id')
            ->get()
            ->each(function (object $practice) use (
                $focusProblemIds,
                $experienceLevelIds,
                $moduleChoiceIds,
                $meditationTypeIds,
            ): void {
                DB::table('practices')
                    ->where('id', $practice->id)
                    ->update([
                        'focus_problem' => $this->valueFromCategoryId($focusProblemIds, $practice->focus_problem_id, 'focus_problem_id'),
                        'experience_level' => $this->valueFromCategoryId($experienceLevelIds, $practice->experience_level_id, 'experience_level_id'),
                        'module_choice' => $this->valueFromCategoryId($moduleChoiceIds, $practice->module_choice_id, 'module_choice_id'),
                        'meditation_type' => $this->valueFromCategoryId($meditationTypeIds, $practice->meditation_type_id, 'meditation_type_id'),
                    ]);
            });

        Schema::table('practices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('focus_problem_id');
            $table->dropConstrainedForeignId('experience_level_id');
            $table->dropConstrainedForeignId('module_choice_id');
            $table->dropConstrainedForeignId('meditation_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->foreignId('focus_problem_id')->nullable()->after('day')->constrained()->nullOnDelete();
            $table->foreignId('experience_level_id')->nullable()->after('focus_problem_id')->constrained()->nullOnDelete();
            $table->foreignId('module_choice_id')->nullable()->after('experience_level_id')->constrained()->nullOnDelete();
            $table->foreignId('meditation_type_id')->nullable()->after('module_choice_id')->constrained()->nullOnDelete();
        });

        $focusProblemIds = array_flip($this->categoryIdsByEnumValue('focus_problems', 'focus_problem'));
        $experienceLevelIds = array_flip($this->categoryIdsByEnumValue('experience_levels', 'experience_level'));
        $moduleChoiceIds = array_flip($this->categoryIdsByEnumValue('module_choices', 'module_choice'));
        $meditationTypeIds = array_flip($this->categoryIdsByEnumValue('meditation_types', 'meditation_type'));

        DB::table('practices')
            ->select([
                'id',
                'focus_problem',
                'experience_level',
                'module_choice',
                'meditation_type',
            ])
            ->orderBy('id')
            ->get()
            ->each(function (object $practice) use (
                $focusProblemIds,
                $experienceLevelIds,
                $moduleChoiceIds,
                $meditationTypeIds,
            ): void {
                DB::table('practices')
                    ->where('id', $practice->id)
                    ->update([
                        'focus_problem_id' => $focusProblemIds[$practice->focus_problem] ?? null,
                        'experience_level_id' => $experienceLevelIds[$practice->experience_level] ?? null,
                        'module_choice_id' => $moduleChoiceIds[$practice->module_choice] ?? null,
                        'meditation_type_id' => $meditationTypeIds[$practice->meditation_type] ?? null,
                    ]);
            });

        Schema::table('practices', function (Blueprint $table) {
            $table->dropColumn([
                'focus_problem',
                'experience_level',
                'module_choice',
                'meditation_type',
            ]);
        });
    }

    private function categoryIdsByEnumValue(string $table, string $translationKey): array
    {
        $knownLabels = $this->knownLabels($translationKey);

        return DB::table($table)
            ->select(['id', 'title'])
            ->get()
            ->mapWithKeys(function (object $record) use ($knownLabels, $table): array {
                $titles = json_decode($record->title, true);

                if (! is_array($titles)) {
                    throw new RuntimeException("Unable to decode {$table}.title for record {$record->id}.");
                }

                foreach ($titles as $title) {
                    $normalizedTitle = mb_strtolower((string) $title);

                    if (isset($knownLabels[$normalizedTitle])) {
                        return [$record->id => $knownLabels[$normalizedTitle]];
                    }
                }

                throw new RuntimeException("Unable to map {$table}.title for record {$record->id} to an enum value.");
            })
            ->all();
    }

    private function knownLabels(string $translationKey): array
    {
        $labels = [];

        foreach (['en', 'ru'] as $locale) {
            /** @var array<string, array<string, string>> $translations */
            $translations = require lang_path("{$locale}/enums.php");

            foreach ($translations[$translationKey] as $value => $label) {
                $labels[mb_strtolower($label)] = $value;
            }
        }

        return $labels;
    }

    private function valueFromCategoryId(array $enumValuesById, mixed $categoryId, string $column): ?string
    {
        if ($categoryId === null) {
            return null;
        }

        if (! isset($enumValuesById[$categoryId])) {
            throw new RuntimeException("Unable to map practice {$column} value [{$categoryId}] to an enum.");
        }

        return $enumValuesById[$categoryId];
    }
};
