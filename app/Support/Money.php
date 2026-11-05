<?php

declare(strict_types=1);

namespace App\Support;

class Money
{
    /**
     * Format a whole Rupiah amount, e.g. 2450000 => "Rp 2.450.000".
     */
    public static function format(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
