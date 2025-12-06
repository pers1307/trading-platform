<?php

namespace App\Service\PercentCalculator;

final readonly class PercentCalculatorRequest
{
    public function __construct(
        public float $principal,
        public float $annualRatePercent,
        public int $termDays,
        public int $daysInYear,
    ) {
    }
}
