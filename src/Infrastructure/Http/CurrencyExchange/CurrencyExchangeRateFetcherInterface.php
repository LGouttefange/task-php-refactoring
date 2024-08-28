<?php

namespace App\Infrastructure\Http\CurrencyExchange;

interface CurrencyExchangeRateFetcherInterface
{
    public function getExchangeRate(string $currency): float;
}
