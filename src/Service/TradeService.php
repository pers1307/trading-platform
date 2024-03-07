<?php

namespace App\Service;

use App\Entity\Trade;
use App\Exception\TradeHasNotClosePriceException;
use App\Exception\TradeHasOpenStatusException;
use App\Exception\TradeHasUnknownTypeException;

class TradeService
{
    /**
     * @throws TradeHasOpenStatusException
     * @throws TradeHasNotClosePriceException
     * @throws TradeHasUnknownTypeException
     */
    public function calculateResult(Trade $trade): float
    {
        if (Trade::STATUS_OPEN === $trade->getStatus()) {
            throw new TradeHasOpenStatusException();
        }
        if (empty($trade->getClosePrice())) {
            throw new TradeHasNotClosePriceException();
        }

        if (Trade::TYPE_LONG === $trade->getType()) {
            return ($trade->getClosePrice() - $trade->getOpenPrice()) * $trade->getLots() * $trade->getStock()->getLotSize();
        }
        if (Trade::TYPE_SHORT === $trade->getType()) {
            return ($trade->getOpenPrice() - $trade->getClosePrice()) * $trade->getLots() * $trade->getStock()->getLotSize();
        }

        throw new TradeHasUnknownTypeException();
    }
}
