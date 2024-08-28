<?php

namespace Tests;

use App\Service\AmountToEuroConverter;
use PHPUnit\Framework\TestCase;

class AmountCurrencyConverterTest extends TestCase
{
    private const AMOUNT = 1000;
    private const RATE = 0.8;
    private const OTHER_CURRENCY = 'GBP';
    private const EUR_CURRENCY = 'EUR';
    private const DELTA = 0.001;
    private AmountToEuroConverter $amountCurrencyConverter;

    protected function setUp(): void
    {
        $this->amountCurrencyConverter = new AmountToEuroConverter();
    }

    public function test_it_converts_other_currency_to_euro_with_rate()
    {
        $newAmount = $this->amountCurrencyConverter->toEuros(self::AMOUNT, self::OTHER_CURRENCY, self::RATE);
        $this->assertEqualsWithDelta(1250, $newAmount, self::DELTA);
    }

    public function test_it_does_not_convert_amounts_in_euros()
    {
        $newAmount = $this->amountCurrencyConverter->toEuros(self::AMOUNT, self::EUR_CURRENCY, self::RATE);
        $this->assertEqualsWithDelta(self::AMOUNT, $newAmount, self::DELTA);
    }

    public function test_it_does_not_convert_amounts_with_invalid_rates()
    {
        $newAmount = $this->amountCurrencyConverter->toEuros(self::AMOUNT, self::OTHER_CURRENCY, 0);
        $this->assertEqualsWithDelta(self::AMOUNT, $newAmount, self::DELTA);
    }
    
    public function test_it_does_not_convert_amounts_with_null_rates()
    {
        $newAmount = $this->amountCurrencyConverter->toEuros(self::AMOUNT, self::OTHER_CURRENCY, null);
        $this->assertEqualsWithDelta(self::AMOUNT, $newAmount, self::DELTA);
    }

}
