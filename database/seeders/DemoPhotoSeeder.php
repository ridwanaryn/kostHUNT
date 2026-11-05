<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Room;
use App\Models\User;
use GdImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoPhotoSeeder extends Seeder
{
    /**
     * Generate a few placeholder gallery images for the demo buildings and rooms.
     */
    public function run(): void
    {
        if (! function_exists('imagecreatetruecolor')) {
            return;
        }

        $owner = User::query()->where('email', 'owner@kostHUNT.test')->first();

        if ($owner === null) {
            return;
        }

        $buildings = $owner->buildings()->with('rooms')->get();

        foreach ($buildings as $building) {
            $this->seedPhotos($building, 3);

            foreach ($building->rooms as $room) {
                $this->seedPhotos($room, 2);
            }
        }
    }

    private function seedPhotos(Building|Room $model, int $count): void
    {
        if ($model->photos()->exists()) {
            return;
        }

        for ($index = 1; $index <= $count; $index++) {
            $path = $this->createImage($model, $index);

            if ($path === null) {
                return;
            }

            $model->photos()->create([
                'path' => $path,
                'is_cover' => $index === 1,
                'sort_order' => $index,
            ]);
        }
    }

    private function createImage(Building|Room $model, int $index): ?string
    {
        $palette = ['#F4F0EA', '#FFE600', '#00F0FF', '#FF90E8', '#38EF7D', '#FF6B4A', '#C4B5FD'];
        $image = imagecreatetruecolor(1200, 800);

        if (! $image instanceof GdImage) {
            return null;
        }

        $background = $this->allocate($image, $palette[($model->id + $index) % count($palette)]);
        imagefilledrectangle($image, 0, 0, 1199, 799, $background);

        $accentOne = $this->allocate($image, $palette[($model->id + $index + 3) % count($palette)]);
        imagefilledrectangle($image, 80, 480, 1120, 720, $accentOne);

        $accentTwo = $this->allocate($image, $palette[($model->id + $index + 5) % count($palette)]);
        imagefilledrectangle($image, 80, 80, 440, 420, $accentTwo);

        $black = (int) imagecolorallocate($image, 0, 0, 0);
        imagesetthickness($image, 16);
        imagerectangle($image, 8, 8, 1191, 791, $black);

        $kind = $model instanceof Building ? 'Building' : 'Room';
        imagestring($image, 5, 100, 520, strtoupper(substr($kind.' '.$model->name, 0, 40)), $black);

        ob_start();
        imagejpeg($image, null, 85);
        $binary = (string) ob_get_clean();
        imagedestroy($image);

        $folder = $model instanceof Building ? 'building' : 'room';
        $path = sprintf('photos/seed/%s-%d-%d.jpg', $folder, $model->id, $index);

        Storage::disk((string) config('kosthunt.uploads.disk'))->put($path, $binary);

        return $path;
    }

    private function allocate(GdImage $image, string $hex): int
    {
        $hex = ltrim($hex, '#');

        return (int) imagecolorallocate(
            $image,
            (int) hexdec(substr($hex, 0, 2)),
            (int) hexdec(substr($hex, 2, 2)),
            (int) hexdec(substr($hex, 4, 2)),
        );
    }
}
