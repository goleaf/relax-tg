<?php

namespace App\Filament\Resources\Practices\Pages;

use App\Filament\Resources\Practices\PracticeResource;
use App\Models\Practice;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditPractice extends EditRecord
{
    protected static string $resource = PracticeResource::class;

    /**
     * @var array<string, string|null>
     */
    private array $originalMediaPaths = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        /** @var Practice $record */
        $record = $this->getRecord();

        $this->originalMediaPaths = [
            'image_path' => $record->image_path,
            'video_path' => $record->video_path,
        ];
    }

    protected function afterSave(): void
    {
        /** @var Practice $record */
        $record = $this->getRecord();

        foreach ($this->originalMediaPaths as $attribute => $path) {
            $currentPath = match ($attribute) {
                'image_path' => $record->image_path,
                'video_path' => $record->video_path,
                default => null,
            };

            if (blank($path) || $path === $currentPath) {
                continue;
            }

            Storage::disk(Practice::mediaDisk())->delete($path);
        }

        $this->originalMediaPaths = [];
    }
}
