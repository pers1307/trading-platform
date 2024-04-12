<?php

namespace App\Dto;

use App\Entity\Trade;

class RiskTrades
{
    public function __construct(
        private readonly array $trades,
        private readonly array $riskTradeNotifications,
    ) {
    }

    /**
     * @return Trade[]
     */
    public function getTrades(): array
    {
        return $this->trades;
    }

    /**
     * @return Notification[]
     */
    public function getRiskTradeNotifications(): array
    {
        return $this->riskTradeNotifications;
    }
}
