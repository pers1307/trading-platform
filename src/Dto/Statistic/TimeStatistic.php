<?php

namespace App\Dto\Statistic;

readonly class TimeStatistic
{
    public function __construct(
        private int $totalTimeIntervalInSecond,
        private int $averageProfitTradeInterval,
        private int $averageLossTradeInterval,
        private int $averageTradeInterval,
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
