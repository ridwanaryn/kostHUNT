<?php

declare(strict_types=1);

namespace Tests\Feature\Owner;

use App\Models\Building;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_upload_a_photo_for_a_building(): void
    {
        Storage::fake('public');

        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();

        $this->actingAs($owner)->post(route('owner.buildings.photos.store', $building), [
            'photos' => [UploadedFile::fake()->image('room.jpg')],
        ])->assertRedirect();

        $photo = $building->photos()->first();

        $this->assertNotNull($photo);
        $this->assertTrue($photo->is_cover);
        Storage::disk('public')->assertExists($photo->path);
    }

    public function test_first_photo_becomes_cover_and_cover_can_change(): void
    {
        Storage::fake('public');

        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();

        $this->actingAs($owner)->post(route('owner.buildings.photos.store', $building), [
            'photos' => [
                UploadedFile::fake()->image('first.jpg'),
                UploadedFile::fake()->image('second.jpg'),
            ],
        ])->assertRedirect();

        $photos = $building->photos()->orderBy('sort_order')->get();

        $this->assertTrue($photos[0]->is_cover);
        $this->assertFalse($photos[1]->is_cover);

        $this->actingAs($owner)
            ->patch(route('owner.photos.cover', $photos[1]))
            ->assertRedirect();

        $this->assertFalse($photos[0]->fresh()->is_cover);
        $this->assertTrue($photos[1]->fresh()->is_cover);
    }

    public function test_owner_can_delete_a_photo(): void
    {
        Storage::fake('public');

        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();

        $this->actingAs($owner)->post(route('owner.buildings.photos.store', $building), [
            'photos' => [UploadedFile::fake()->image('room.jpg')],
        ]);

        $photo = $building->photos()->first();
        $this->assertNotNull($photo);

        $this->actingAs($owner)
            ->delete(route('owner.photos.destroy', $photo))
            ->assertRedirect();

        $this->assertDatabaseMissing('photos', ['id' => $photo->id]);
        Storage::disk('public')->assertMissing($photo->path);
    }

    public function test_owner_can_upload_a_photo_for_a_room(): void
    {
        Storage::fake('public');

        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create();

        $this->actingAs($owner)->post(route('owner.rooms.photos.store', $room), [
            'photos' => [UploadedFile::fake()->image('room.jpg')],
        ])->assertRedirect();

        $this->assertSame(1, $room->photos()->count());
    }

    public function test_photo_must_be_an_image(): void
    {
        Storage::fake('public');

        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();

        $this->actingAs($owner)->post(route('owner.buildings.photos.store', $building), [
            'photos' => [UploadedFile::fake()->create('document.pdf', 10)],
        ])->assertSessionHasErrors('photos.0');
    }

    public function test_other_owner_cannot_upload_a_photo(): void
    {
        Storage::fake('public');

        $owner = User::factory()->owner()->create();
        $other = User::factory()->owner()->create();
        $building = Building::factory()->for($other, 'owner')->create();

        $this->actingAs($owner)->post(route('owner.buildings.photos.store', $building), [
            'photos' => [UploadedFile::fake()->image('room.jpg')],
        ])->assertForbidden();
    }

    public function test_other_owner_cannot_delete_a_photo(): void
    {
        Storage::fake('public');

        $owner = User::factory()->owner()->create();
        $other = User::factory()->owner()->create();
        $building = Building::factory()->for($other, 'owner')->create();

        $this->actingAs($other)->post(route('owner.buildings.photos.store', $building), [
            'photos' => [UploadedFile::fake()->image('room.jpg')],
        ]);

        $photo = $building->photos()->first();
        $this->assertNotNull($photo);

        $this->actingAs($owner)
            ->delete(route('owner.photos.destroy', $photo))
            ->assertForbidden();
    }
}
