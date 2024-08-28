<?php

namespace Tests;

use App\Service\CommissionAmountCalculator;
use PHPUnit\Framework\TestCase;

class CommissionAmountCalculatorTest extends TestCase
{
    private const AMOUNT = 500;
    private CommissionAmountCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new CommissionAmountCalculator(0.5, 1);
    }

    public function test_it_applies_eu_rate()
    {
        $this->assertEqualsWithDelta(250, $this->calculator->calculateCommission(self::AMOUNT, true), 0.001);
    }

    public function test_it_applies_rate_outisde_eu()
    {
        $this->assertEqualsWithDelta(self::AMOUNT, $this->calculator->calculateCommission(self::AMOUNT, false), 0.001);
    }

}
