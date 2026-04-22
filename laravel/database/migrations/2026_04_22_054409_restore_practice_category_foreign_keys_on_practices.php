<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->foreignId('focus_problem_id')->nullable()->after('day')->constrained()->nullOnDelete();
            $table->foreignId('experience_level_id')->nullable()->after('focus_problem_id')->constrained()->nullOnDelete();
            $table->foreignId('module_choice_id')->nullable()->after('experience_level_id')->constrained()->nullOnDelete();
            $table->foreignId('meditation_type_id')->nullable()->after('module_choice_id')->constrained()->nullOnDelete();
        });

        $focusProblemIds = $this->categoryIdsByStoredValue('focus_problems', 'focus_problem');
        $experienceLevelIds = $this->categoryIdsByStoredValue('experience_levels', 'experience_level');
        $moduleChoiceIds = $this->categoryIdsByStoredValue('module_choices', 'module_choice');
        $meditationTypeIds = $this->categoryIdsByStoredValue('meditation_types', 'meditation_type');

        DB::table('practices')
            ->select(['id', 'focus_problem', 'experience_level', 'module_choice', 'meditation_type'])
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
                        'focus_problem_id' => $this->idFromStoredValue($focusProblemIds, $practice->focus_problem, 'focus_problem'),
                        'experience_level_id' => $this->idFromStoredValue($experienceLevelIds, $practice->experience_level, 'experience_level'),
                        'module_choice_id' => $this->idFromStoredValue($moduleChoiceIds, $practice->module_choice, 'module_choice'),
                        'meditation_type_id' => $this->idFromStoredValue($meditationTypeIds, $practice->meditation_type, 'meditation_type'),
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

    public function down(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->enum('focus_problem', array_keys($this->translations('focus_problem')))->nullable()->after('day');
            $table->enum('experience_level', array_keys($this->translations('experience_level')))->nullable()->after('focus_problem');
            $table->enum('module_choice', array_keys($this->translations('module_choice')))->nullable()->after('experience_level');
            $table->enum('meditation_type', array_keys($this->translations('meditation_type')))->nullable()->after('module_choice');
        });

        $focusProblemValues = $this->storedValuesByCategoryId('focus_problems', 'focus_problem');
        $experienceLevelValues = $this->storedValuesByCategoryId('experience_levels', 'experience_level');
        $moduleChoiceValues = $this->storedValuesByCategoryId('module_choices', 'module_choice');
        $meditationTypeValues = $this->storedValuesByCategoryId('meditation_types', 'meditation_type');

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
                $focusProblemValues,
                $experienceLevelValues,
                $moduleChoiceValues,
                $meditationTypeValues,
            ): void {
                DB::table('practices')
                    ->where('id', $practice->id)
                    ->update([
                        'focus_problem' => $focusProblemValues[$practice->focus_problem_id] ?? null,
                        'experience_level' => $experienceLevelValues[$practice->experience_level_id] ?? null,
                        'module_choice' => $moduleChoiceValues[$practice->module_choice_id] ?? null,
                        'meditation_type' => $meditationTypeValues[$practice->meditation_type_id] ?? null,
                    ]);
            });

        Schema::table('practices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('focus_problem_id');
            $table->dropConstrainedForeignId('experience_level_id');
            $table->dropConstrainedForeignId('module_choice_id');
            $table->dropConstrainedForeignId('meditation_type_id');
        });
    }

    private function categoryIdsByStoredValue(string $table, string $translationKey): array
    {
        $translations = $this->translations($translationKey);

        return DB::table($table)
            ->select(['id', 'title'])
            ->orderBy('id')
            ->get()
            ->mapWithKeys(function (object $record) use ($table, $translations): array {
                $titles = json_decode($record->title, true);

                if (! is_array($titles)) {
                    throw new RuntimeException("Unable to decode {$table}.title for record {$record->id}.");
                }

                $englishTitle = $titles['en'] ?? null;

                if (! is_string($englishTitle)) {
                    throw new RuntimeException("Missing English title for {$table} record {$record->id}.");
                }

                $value = array_search($englishTitle, $translations, true);

                if ($value === false) {
                    throw new RuntimeException("Unable to map {$table} title [{$englishTitle}] to a stored value.");
                }

                return [$value => $record->id];
            })
            ->all();
    }

    private function storedValuesByCategoryId(string $table, string $translationKey): array
    {
        $translations = $this->translations($translationKey);

        return DB::table($table)
            ->select(['id', 'title'])
            ->orderBy('id')
            ->get()
            ->mapWithKeys(function (object $record) use ($table, $translations): array {
                $titles = json_decode($record->title, true);

                if (! is_array($titles)) {
                    throw new RuntimeException("Unable to decode {$table}.title for record {$record->id}.");
                }

                $englishTitle = $titles['en'] ?? null;

                if (! is_string($englishTitle)) {
                    throw new RuntimeException("Missing English title for {$table} record {$record->id}.");
                }

                $value = array_search($englishTitle, $translations, true);

                if ($value === false) {
                    throw new RuntimeException("Unable to map {$table} title [{$englishTitle}] to a stored value.");
                }

                return [$record->id => $value];
            })
            ->all();
    }

    private function idFromStoredValue(array $idsByStoredValue, mixed $value, string $column): ?int
    {
        if ($value === null) {
            return null;
        }

        if (! isset($idsByStoredValue[$value])) {
            throw new RuntimeException("Unable to map practice {$column} value [{$value}] to a relation id.");
        }

        return $idsByStoredValue[$value];
    }

    /**
     * @return array<string, string>
     */
    private function translations(string $translationKey): array
    {
        /** @var array<string, array<string, string>> $translations */
        $translations = require lang_path('en/enums.php');

        return $translations[$translationKey];
    }
};
