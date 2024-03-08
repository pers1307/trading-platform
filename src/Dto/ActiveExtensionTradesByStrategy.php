<?php

namespace App\Dto;

use App\Entity\Accaunt;
use App\Entity\Strategy;

class ActiveExtensionTradesByStrategy
{
    /**
     * @param ExtensionTrade[] $extensionTrades
     */
    public function __construct(
        private readonly array $extensionTrades,
        private readonly Strategy $strategy,
        private readonly Accaunt $accaunt,
    ) {
    }

    public function getExtensionTrades(): array
    {
        return $this->extensionTrades;
    }

    public function getStrategy(): Strategy
    {
        return $this->strategy;
    }

    public function getAccaunt(): Accaunt
    {
        return $this->accaunt;
    }

}
