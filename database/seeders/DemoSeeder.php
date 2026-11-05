<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\InvoiceStatus;
use App\Enums\MaintenanceStatus;
use App\Enums\PeriodType;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Building;
use App\Models\Conversation;
use App\Models\Facility;
use App\Models\MaintenanceRequest;
use App\Models\Message;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoSeeder extends Seeder
{
    /**
     * Seed a realistic, repeatable demo scenario.
     */
    public function run(): void
    {
        $owner = User::query()->firstOrCreate(
            ['email' => 'owner@ngekost.test'],
            ['name' => 'Demo Owner', 'password' => 'password', 'role' => UserRole::Owner],
        );

        $tenant = User::query()->firstOrCreate(
            ['email' => 'tenant@ngekost.test'],
            ['name' => 'Demo Tenant', 'password' => 'password', 'role' => UserRole::Tenant],
        );

        $facilityIds = Facility::query()->pluck('id');

        $melati = $owner->buildings()->firstOrCreate(
            ['name' => 'Kost Melati'],
            [
                'description' => 'A calm, clean kost close to campus with friendly neighbours.',
                'street' => 'Jl. Melati No. 10',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'house_rules' => 'No smoking indoors. Quiet hours after 10 PM.',
            ],
        );

        $anggrek = $owner->buildings()->firstOrCreate(
            ['name' => 'Kost Anggrek'],
            [
                'description' => 'Modern rooms in the heart of the city.',
                'street' => 'Jl. Anggrek No. 5',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'house_rules' => 'No pets. Keep common areas clean.',
            ],
        );

        $room101 = $this->room($melati, 'Room 101', 1_500_000, 15_000_000, $facilityIds->random(4)->all());
        $room102 = $this->room($melati, 'Room 102', 1_750_000, 17_500_000, $facilityIds->random(3)->all());
        $this->room($melati, 'Room 103', 2_000_000, null, $facilityIds->random(2)->all());
        $room201 = $this->room($anggrek, 'Room 201', 2_500_000, 25_000_000, $facilityIds->random(3)->all());

        $start = Carbon::now()->startOfDay()->subDays(10);

        $activeBooking = $tenant->bookings()->firstOrCreate(
            ['room_id' => $room101->id, 'start_date' => $start->toDateString()],
            [
                'period_type' => PeriodType::Monthly,
                'duration' => 2,
                'end_date' => $start->copy()->addMonthsNoOverflow(2)->subDay()->toDateString(),
                'rent_amount' => 3_000_000,
                'status' => BookingStatus::Active,
            ],
        );

        $this->seedInvoices($activeBooking, $start);

        $tenant->bookings()->firstOrCreate(
            ['room_id' => $room201->id, 'start_date' => Carbon::now()->addDays(10)->toDateString()],
            [
                'period_type' => PeriodType::Monthly,
                'duration' => 1,
                'end_date' => Carbon::now()->addDays(10)->addMonthNoOverflow()->subDay()->toDateString(),
                'rent_amount' => 2_500_000,
                'status' => BookingStatus::Upcoming,
            ],
        );

        if (! $activeBooking->review()->exists()) {
            Review::query()->create([
                'booking_id' => $activeBooking->id,
                'room_id' => $room101->id,
                'tenant_id' => $tenant->id,
                'rating' => 5,
                'comment' => 'Clean, quiet, and the owner responds fast. Would rent again!',
            ]);
        }

        $tenant->favoriteRooms()->syncWithoutDetaching([$room102->id]);

        $conversation = Conversation::query()->firstOrCreate([
            'tenant_id' => $tenant->id,
            'owner_id' => $owner->id,
            'room_id' => $room101->id,
        ]);

        if ($conversation->messages()->count() === 0) {
            Message::query()->create([
                'conversation_id' => $conversation->id,
                'sender_id' => $tenant->id,
                'body' => 'Hi! Is the water heater working in Room 101?',
            ]);
            Message::query()->create([
                'conversation_id' => $conversation->id,
                'sender_id' => $owner->id,
                'body' => 'Yes, it was serviced last week. Let me know if anything comes up!',
            ]);
            $conversation->touch();
        }

        MaintenanceRequest::query()->firstOrCreate(
            ['booking_id' => $activeBooking->id, 'title' => 'Leaking bathroom tap'],
            [
                'room_id' => $room101->id,
                'tenant_id' => $tenant->id,
                'description' => 'The bathroom tap keeps dripping at night.',
                'status' => MaintenanceStatus::InProgress,
                'owner_note' => 'Technician scheduled for tomorrow morning.',
            ],
        );
    }

    private function seedInvoices(Booking $booking, Carbon $start): void
    {
        $invoices = app(InvoiceService::class);

        if ($booking->invoices()->count() === 0) {
            $invoices->generateInitial($booking);
        }

        $first = $booking->invoices()->orderBy('period_start')->first();

        if ($first !== null && $first->status === InvoiceStatus::Unpaid) {
            $invoices->markPaid($first);
        }

        $secondStart = $start->copy()->addMonthNoOverflow();

        if (! $booking->invoices()->whereDate('period_start', $secondStart->toDateString())->exists()) {
            $invoices->generateForPeriod($booking, $secondStart);
        }
    }

    /**
     * @param  array<int, int>  $facilityIds
     */
    private function room(Building $building, string $name, int $monthly, ?int $yearly, array $facilityIds): Room
    {
        $room = $building->rooms()->firstOrCreate(
            ['name' => $name],
            [
                'description' => 'A comfortable room with everything you need.',
                'size_sqm' => fake()->numberBetween(10, 24),
                'price_monthly' => $monthly,
                'price_yearly' => $yearly,
                'electricity_included' => true,
                'water_included' => true,
                'is_active' => true,
            ],
        );

        $room->facilities()->syncWithoutDetaching($facilityIds);

        return $room;
    }
}
