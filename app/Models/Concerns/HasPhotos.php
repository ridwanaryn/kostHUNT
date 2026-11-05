<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Photo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasPhotos
{
    /**
     * @return MorphMany<Photo, $this>
     */
    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'imageable')->orderBy('sort_order');
    }

    public function coverPhoto(): ?Photo
    {
        return $this->photos->firstWhere('is_cover', true) ?? $this->photos->first();
    }
}
