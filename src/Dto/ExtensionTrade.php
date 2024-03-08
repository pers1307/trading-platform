<?php

namespace App\Dto;

use App\Entity\Trade;

class ExtensionTrade
{
    private Trade $trade;

    private float $tradeResult;

    /**
     * @todo перенести в dto статистики
     */
    private ?float $cumulativeTotal;

    public function __construct(Trade $trade, float $tradeResult, ?float $cumulativeTotal = null)
    {
        $this->trade = $trade;
        $this->tradeResult = $tradeResult;
        $this->cumulativeTotal = $cumulativeTotal;
    }

    public function getTrade(): Trade
    {
        return $this->trade;
    }

    public function getTradeResult(): float
    {
        return $this->tradeResult;
    }

    public function getCumulativeTotal(): float
    {
        return $this->cumulativeTotal;
    }
}
