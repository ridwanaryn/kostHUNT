<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Building;
use App\Models\Photo;
use App\Models\Room;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PhotoService
{
    /**
     * @param  array<int, mixed>  $files
     * @return array<int, Photo>
     */
    public function storeMany(Building|Room $model, array $files, bool $firstIsCover = true): array
    {
        $photos = [];

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $photos[] = $this->store($model, $file);
        }

        if ($firstIsCover && $photos !== [] && ! $this->hasCover($model)) {
            $photos[0]->update(['is_cover' => true]);
        }

        return $photos;
    }

    public function store(Building|Room $model, UploadedFile $file, bool $isCover = false): Photo
    {
        if ($isCover) {
            $this->clearCover($model);
        }

        $path = $file->store($this->directory($model), $this->disk());
        $sortOrder = (int) $model->photos()->max('sort_order');

        return $model->photos()->create([
            'path' => $path,
            'is_cover' => $isCover,
            'sort_order' => $sortOrder + 1,
        ]);
    }

    public function setCover(Photo $photo): void
    {
        $model = $photo->imageable;

        if ($model === null) {
            return;
        }

        $this->clearCover($model);
        $photo->update(['is_cover' => true]);
    }

    public function delete(Photo $photo): void
    {
        $wasCover = $photo->is_cover;
        $model = $photo->imageable;

        Storage::disk($this->disk())->delete($photo->path);
        $photo->delete();

        if ($wasCover && $model !== null) {
            $model->photos()->first()?->update(['is_cover' => true]);
        }
    }

    public function remainingSlots(Building|Room $model): int
    {
        return max(0, $this->maxPhotos($model) - $model->photos()->count());
    }

    private function hasCover(Building|Room $model): bool
    {
        return $model->photos()->where('is_cover', true)->exists();
    }

    private function clearCover(Building|Room $model): void
    {
        $model->photos()->where('is_cover', true)->update(['is_cover' => false]);
    }

    private function maxPhotos(Building|Room $model): int
    {
        return match (true) {
            $model instanceof Building => (int) config('kosthunt.uploads.max_per_building'),
            $model instanceof Room => (int) config('kosthunt.uploads.max_per_room'),
        };
    }

    private function directory(Building|Room $model): string
    {
        return match (true) {
            $model instanceof Building => 'photos/buildings',
            $model instanceof Room => 'photos/rooms',
        };
    }

    private function disk(): string
    {
        return (string) config('kosthunt.uploads.disk');
    }
}
