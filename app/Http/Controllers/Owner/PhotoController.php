<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StorePhotoRequest;
use App\Models\Building;
use App\Models\Photo;
use App\Models\Room;
use App\Services\PhotoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class PhotoController extends Controller
{
    public function __construct(private readonly PhotoService $photos) {}

    public function storeBuilding(StorePhotoRequest $request, Building $building): RedirectResponse
    {
        $this->authorize('update', $building);

        $this->storePhotos($building, $request);

        return back()->with('status', 'Photos uploaded.');
    }

    public function storeRoom(StorePhotoRequest $request, Room $room): RedirectResponse
    {
        $room->loadMissing('building');
        $this->authorize('update', $room);

        $this->storePhotos($room, $request);

        return back()->with('status', 'Photos uploaded.');
    }

    public function setCover(Photo $photo): RedirectResponse
    {
        $this->loadImageable($photo);
        $this->authorize('update', $photo);

        $this->photos->setCover($photo);

        return back()->with('status', 'Cover photo updated.');
    }

    public function destroy(Photo $photo): RedirectResponse
    {
        $this->loadImageable($photo);
        $this->authorize('delete', $photo);

        $this->photos->delete($photo);

        return back()->with('status', 'Photo deleted.');
    }

    private function storePhotos(Building|Room $model, StorePhotoRequest $request): void
    {
        $files = $request->file('photos');

        if (! is_array($files)) {
            return;
        }

        $remaining = $this->photos->remainingSlots($model);

        if (count($files) > $remaining) {
            throw ValidationException::withMessages([
                'photos' => __('You can upload :count more photo(s).', ['count' => $remaining]),
            ]);
        }

        $this->photos->storeMany($model, $files);
    }

    private function loadImageable(Photo $photo): void
    {
        $photo->loadMissing('imageable');

        $imageable = $photo->imageable;

        if ($imageable instanceof Room) {
            $imageable->loadMissing('building');
        }
    }
}
