<?php

namespace App\Service;

interface AmountToEuroConverterInterface
{
    public function toEuros(float $amount, string $currency, ?float $rate): float;
}
