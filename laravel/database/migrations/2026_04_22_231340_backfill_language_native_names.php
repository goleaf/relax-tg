<?php

use App\Models\Language;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Language::query()
            ->where(fn ($query) => $query
                ->whereNull('native_name')
                ->orWhere('native_name', ''))
            ->select(['id', 'code', 'name', 'native_name'])
            ->lazyById()
            ->each(function (Language $language): void {
                $language->forceFill([
                    'native_name' => Language::nativeName($language->code, $language->name),
                ])->saveQuietly();
            });
    }

    public function down(): void
    {
        //
    }
};
