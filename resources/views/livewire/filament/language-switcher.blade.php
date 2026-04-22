<div class="flex items-center gap-1 px-2">
    @foreach ($this->languages as $language)
        <button
            wire:click="switchLocale('{{ $language->code }}')"
            title="{{ $language->name }}"
            @class([
                'flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-sm font-medium transition-colors',
                'bg-primary-500/10 text-primary-700 dark:text-primary-400 ring-1 ring-primary-500/30' => $activeLocale === $language->code,
                'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5' => $activeLocale !== $language->code,
            ])
        >
            <x-dynamic-component
                :component="'flag-language-' . strtolower($language->code)"
                class="h-4 w-4 shrink-0"
            />
            <span class="hidden sm:inline">{{ strtoupper($language->code) }}</span>
        </button>
    @endforeach
</div>
