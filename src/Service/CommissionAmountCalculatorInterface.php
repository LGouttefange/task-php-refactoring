<?php

namespace App\Service;

interface CommissionAmountCalculatorInterface
{
    public function calculateCommission(float $amount, bool $isIssuedInEu,): float;
}
