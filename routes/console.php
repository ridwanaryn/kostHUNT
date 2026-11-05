<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('kosthunt:generate-invoices')->daily();
Schedule::command('kosthunt:send-billing-reminders')->daily();
Schedule::command('kosthunt:update-booking-statuses')->daily();
