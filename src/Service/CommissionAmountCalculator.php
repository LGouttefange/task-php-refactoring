<?php

namespace App\Service;

class CommissionAmountCalculator implements CommissionAmountCalculatorInterface
{
    public function __construct(
        private float $commissionRateInEu,
        private float $commissionRateOutsideEu,
    )
    {
    }

    public function calculateCommission(
        float $amount,
        bool $isIssuedInEu,
    ): float
    {
        $rate = $isIssuedInEu ? $this->commissionRateInEu : $this->commissionRateOutsideEu;
        return ceil($amount * $rate * 100)/100;
    }
}
