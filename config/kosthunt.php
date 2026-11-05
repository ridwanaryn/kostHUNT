<?php

declare(strict_types=1);

return [
    'uploads' => [
        'disk' => env('KOSTHUNT_UPLOAD_DISK', 'public'),
        'max_size_kb' => 2048,
        'max_per_building' => 8,
        'max_per_room' => 6,
    ],

    'pagination' => [
        'per_page' => 12,
    ],

    'booking' => [
        'advance_days' => 90,
        'max_duration' => 24,
    ],

    'billing' => [
        'reminder_days' => 3,
        'generate_days_before' => 7,
    ],
];
