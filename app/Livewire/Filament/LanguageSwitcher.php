<?php

namespace App\Livewire\Filament;

use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    /** @var string The currently active locale in the admin panel session. */
    public string $activeLocale;

    public function mount(): void
    {
        $this->activeLocale = session('admin_locale', app()->getLocale());
    }

    public function switchLocale(string $code): void
    {
        session(['admin_locale' => $code]);
        $this->activeLocale = $code;
        $this->dispatch('locale-switched', locale: $code);
    }

    /**
     * @return Collection<int, Language>
     */
    public function getLanguagesProperty(): Collection
    {
        return Language::where('is_enabled', true)->get();
    }

    public function render(): View
    {
        return view('livewire.filament.language-switcher');
    }
}
