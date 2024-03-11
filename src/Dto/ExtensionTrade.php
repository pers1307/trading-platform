<?php

namespace App\Dto;

use App\Entity\Trade;

class ExtensionTrade
{
    private Trade $trade;

    private float $tradeResult;

    public function __construct(Trade $trade, float $tradeResult)
    {
        $this->trade = $trade;
        $this->tradeResult = $tradeResult;
    }

    public function getTrade(): Trade
    {
        return $this->trade;
    }

    public function getTradeResult(): float
    {
        return $this->tradeResult;
    }
}
