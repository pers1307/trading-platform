<?php

namespace App\Dto\Statistic;

readonly class CountStatistic
{
    public function __construct(
        private int $countTrades,
        private int $countProfitTrades,
        private int $countLossTrades,
        private float $persentProfitTrades,
        private float $persentLossTrades,
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
