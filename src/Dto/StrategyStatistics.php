<?php

namespace App\Dto;

class StrategyStatistics
{
    private readonly int $countTrades;

    /**
     * @param ExtensionTrade[] $extensionTrades
     */
    public function __construct(
        private readonly array $extensionTrades,
        private readonly int $countLossTrades,
        private readonly int $countProfitTrades,
        private readonly float $averageProfit,
        private readonly float $averageLoss,
        private readonly float $expectedValue
    ) {
        $this->countTrades = $this->countLossTrades + $this->countProfitTrades;
    }

    public function getExtensionTrades(): array
    {
        return $this->extensionTrades;
    }

    public function getCountLossTrades(): int
    {
        return $this->countLossTrades;
    }

    public function getCountProfitTrades(): int
    {
        return $this->countProfitTrades;
    }

    public function getAverageProfit(): float
    {
        return $this->averageProfit;
    }

    public function getAverageLoss(): float
    {
        return $this->averageLoss;
    }

    public function getExpectedValue(): float
    {
        return $this->expectedValue;
    }

    public function getCountTrades(): int
    {
        return $this->countTrades;
    }
}
