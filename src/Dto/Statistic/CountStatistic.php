<?php

namespace App\Dto\Statistic;

class CountStatistic
{
    public function __construct(
        private readonly int $countTrades,
        private readonly int $countProfitTrades,
        private readonly int $countLossTrades,
        private readonly float $persentProfitTrades,
        private readonly float $persentLossTrades,
    ) {
    }

    public function getCountTrades(): int
    {
        return $this->countTrades;
    }

    public function getCountProfitTrades(): int
    {
        return $this->countProfitTrades;
    }

    public function getCountLossTrades(): int
    {
        return $this->countLossTrades;
    }

    public function getPersentProfitTrades(): float
    {
        return $this->persentProfitTrades;
    }

    public function getPersentLossTrades(): float
    {
        return $this->persentLossTrades;
    }
}
