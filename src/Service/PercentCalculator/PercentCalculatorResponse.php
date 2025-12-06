<?php

namespace App\Service\PercentCalculator;

final readonly class PercentCalculatorResponse
{
    public function __construct(
        public float $interestAmount,
        public float $totalAmount,
    ) {
    }
}
