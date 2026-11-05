<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_it_formats_whole_rupiah_amounts(): void
    {
        $this->assertSame('Rp 2.450.000', Money::format(2_450_000));
        $this->assertSame('Rp 0', Money::format(0));
        $this->assertSame('Rp 1.500.000', Money::format(1_500_000));
    }
}
