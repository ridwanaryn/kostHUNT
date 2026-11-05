<?php

declare(strict_types=1);

namespace Tests\Feature\Chat;

use App\Models\Building;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_start_a_conversation_from_a_room(): void
    {
        [$tenant, $owner, $room] = $this->participants();

        $response = $this->actingAs($tenant)->post(route('tenant.chat.start', $room));

        $conversation = Conversation::query()->firstOrFail();

        $response->assertRedirect(route('chat.show', $conversation));
        $this->assertSame($tenant->id, $conversation->tenant_id);
        $this->assertSame($owner->id, $conversation->owner_id);
        $this->assertSame($room->id, $conversation->room_id);
    }

    public function test_starting_a_conversation_twice_reuses_it(): void
    {
        [$tenant, , $room] = $this->participants();

        $this->actingAs($tenant)->post(route('tenant.chat.start', $room));
        $this->actingAs($tenant)->post(route('tenant.chat.start', $room));

        $this->assertDatabaseCount('conversations', 1);
    }

    public function test_owner_cannot_start_a_conversation(): void
    {
        [, $owner, $room] = $this->participants();

        $this->actingAs($owner)
            ->post(route('tenant.chat.start', $room))
            ->assertForbidden();
    }

    public function test_participants_can_view_the_conversation(): void
    {
        [$tenant, $owner, $room] = $this->participants();
        $conversation = $this->conversation($tenant, $owner, $room);

        $this->actingAs($tenant)->get(route('chat.show', $conversation))->assertOk();
        $this->actingAs($owner)->get(route('chat.show', $conversation))->assertOk();
    }

    public function test_unrelated_user_cannot_view_the_conversation(): void
    {
        [$tenant, $owner, $room] = $this->participants();
        $conversation = $this->conversation($tenant, $owner, $room);
        $stranger = User::factory()->tenant()->create();

        $this->actingAs($stranger)
            ->get(route('chat.show', $conversation))
            ->assertForbidden();
    }

    public function test_participants_can_send_messages(): void
    {
        [$tenant, $owner, $room] = $this->participants();
        $conversation = $this->conversation($tenant, $owner, $room);

        $this->actingAs($tenant)
            ->post(route('chat.messages.store', $conversation), ['body' => 'Hello!'])
            ->assertRedirect(route('chat.show', $conversation));

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $tenant->id,
            'body' => 'Hello!',
        ]);

        $this->actingAs($owner)
            ->post(route('chat.messages.store', $conversation), ['body' => 'Hi there!'])
            ->assertRedirect();

        $this->assertDatabaseCount('messages', 2);
    }

    public function test_unrelated_user_cannot_send_messages(): void
    {
        [$tenant, $owner, $room] = $this->participants();
        $conversation = $this->conversation($tenant, $owner, $room);
        $stranger = User::factory()->tenant()->create();

        $this->actingAs($stranger)
            ->post(route('chat.messages.store', $conversation), ['body' => 'Spam'])
            ->assertForbidden();
    }

    public function test_message_body_is_required(): void
    {
        [$tenant, $owner, $room] = $this->participants();
        $conversation = $this->conversation($tenant, $owner, $room);

        $this->actingAs($tenant)
            ->post(route('chat.messages.store', $conversation), ['body' => ''])
            ->assertSessionHasErrors('body');
    }

    public function test_viewing_a_conversation_marks_incoming_messages_as_read(): void
    {
        [$tenant, $owner, $room] = $this->participants();
        $conversation = $this->conversation($tenant, $owner, $room);

        $message = Message::factory()->create([
            'conversation_id' => $conversation->id,
            'sender_id' => $owner->id,
            'body' => 'Are you still interested?',
            'read_at' => null,
        ]);

        $this->actingAs($tenant)
            ->get(route('chat.show', $conversation))
            ->assertOk();

        $this->assertNotNull($message->fresh()->read_at);
    }

    public function test_inbox_lists_conversations_with_the_latest_message(): void
    {
        [$tenant, $owner, $room] = $this->participants();
        $conversation = $this->conversation($tenant, $owner, $room);

        Message::factory()->create([
            'conversation_id' => $conversation->id,
            'sender_id' => $owner->id,
            'body' => 'Is the room still available?',
        ]);

        $this->actingAs($tenant)
            ->get(route('chat.index'))
            ->assertOk()
            ->assertSee($owner->name)
            ->assertSee('Is the room still available?');
    }

    public function test_guest_is_redirected_from_the_inbox(): void
    {
        $this->get(route('chat.index'))->assertRedirect(route('login'));
    }

    /**
     * @return array{0: User, 1: User, 2: Room}
     */
    private function participants(): array
    {
        $owner = User::factory()->owner()->create();
        $building = Building::factory()->for($owner, 'owner')->create();
        $room = Room::factory()->for($building)->create(['is_active' => true]);
        $tenant = User::factory()->tenant()->create();

        return [$tenant, $owner, $room];
    }

    private function conversation(User $tenant, User $owner, Room $room): Conversation
    {
        return Conversation::factory()->create([
            'tenant_id' => $tenant->id,
            'owner_id' => $owner->id,
            'room_id' => $room->id,
        ]);
    }
}
