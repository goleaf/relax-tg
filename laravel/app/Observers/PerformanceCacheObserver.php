<?php

namespace App\Observers;

use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\Language;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use App\Models\Practice;
use Illuminate\Database\Eloquent\Model;

class PerformanceCacheObserver
{
    public function saved(Model $model): void
    {
        $this->invalidateCache($model);
    }

    public function deleted(Model $model): void
    {
        $this->invalidateCache($model);
    }

    private function invalidateCache(Model $model): void
    {
        if ($model instanceof Practice) {
            Practice::bumpAggregateCacheVersion();

            return;
        }

        if ($model instanceof Language) {
            Language::bumpContentCacheVersion();

            return;
        }

        if (($model instanceof FocusProblem)
            || ($model instanceof ExperienceLevel)
            || ($model instanceof ModuleChoice)
            || ($model instanceof MeditationType)) {
            Practice::bumpTaxonomyCacheVersion();
        }
    }
}
