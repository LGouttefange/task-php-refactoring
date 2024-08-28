<?php

namespace App\Service;

class AmountToEuroConverter implements AmountToEuroConverterInterface
{
    public function toEuros(
        float $amount,
        string $currency,
        ?float $rate,
    ): float
    {
        if($currency === 'EUR') {
            return $amount;
        }
        
        if(empty($rate) || $rate <= 0) {
            return $amount;
        }
        
        
        return $amount / $rate;
    }
}
