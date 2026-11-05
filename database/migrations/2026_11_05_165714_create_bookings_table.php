<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('renewed_from_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->string('period_type');
            $table->unsignedInteger('duration');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('rent_amount');
            $table->string('status');
            $table->timestamp('termination_requested_at')->nullable();
            $table->date('requested_move_out_date')->nullable();
            $table->text('termination_reason')->nullable();
            $table->text('termination_owner_note')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index(['room_id', 'start_date', 'end_date']);
            $table->index(['tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
