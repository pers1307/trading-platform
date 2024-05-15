<?php

namespace App\Dto\Statistic;

class ExpectedStatistic
{
    public function __construct(
        private readonly float $expectedValue,
        private readonly float $averageProfit,
        private readonly float $averageLoss,
    ) {
    }

    public function getExpectedValue(): float
    {
        return $this->expectedValue;
    }

    public function getAverageProfit(): float
    {
        return $this->averageProfit;
    }

    public function getAverageLoss(): float
    {
        return $this->averageLoss;
    }
}
