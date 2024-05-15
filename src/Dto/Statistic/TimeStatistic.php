<?php

namespace App\Dto\Statistic;

class TimeStatistic
{
    public function __construct(
        private readonly int $totalTimeIntervalInSecond,
        private readonly int $averageProfitTradeInterval,
        private readonly int $averageLossTradeInterval,
        private readonly int $averageTradeInterval,
    ) {
    }

    public function getTotalTimeIntervalInSecond(): int
    {
        return $this->totalTimeIntervalInSecond;
    }

    public function getAverageProfitTradeInterval(): int
    {
        return $this->averageProfitTradeInterval;
    }

    public function getAverageLossTradeInterval(): int
    {
        return $this->averageLossTradeInterval;
    }

    public function getAverageTradeInterval(): int
    {
        return $this->averageTradeInterval;
    }
}
