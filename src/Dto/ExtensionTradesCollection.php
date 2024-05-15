<?php

namespace App\Dto;

use App\Dto\Statistic\Statistic;

class ExtensionTradesCollection
{
    /**
     * @param ExtensionTrade[] $extensionTrades
     */
    public function __construct(
        private readonly array $extensionTrades,
        private readonly ?Statistic $statistic,
        private readonly ?Graph $graph,
        private readonly ?array $cumulativeTotalArray,
    ) {
    }

    /**
     * @return ExtensionTrade[]
     */
    public function getExtensionTrades(): array
    {
        return $this->extensionTrades;
    }

    public function getStatistic(): ?Statistic
    {
        return $this->statistic;
    }

    public function getGraph(): ?Graph
    {
        return $this->graph;
    }

    public function getCumulativeTotalArray(): ?array
    {
        return $this->cumulativeTotalArray;
    }
}
