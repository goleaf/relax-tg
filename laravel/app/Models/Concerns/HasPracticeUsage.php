<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @phpstan-require-extends Model
 */
trait HasPracticeUsage
{
    public function isInUse(): bool
    {
        $practiceCount = $this->getAttribute('practices_count');

        if (is_numeric($practiceCount)) {
            return (int) $practiceCount > 0;
        }

        return $this->practices()->exists();
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeWithPracticeUsage(Builder $query): Builder
    {
        return $query->withCount('practices');
    }
}
