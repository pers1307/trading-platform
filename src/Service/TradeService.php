<?php

namespace App\Service;

use App\Entity\Trade;
use App\Exception\StockHasNotPriceException;
use App\Exception\TradeHasCloseStatusException;
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

    /**
     * @throws TradeHasCloseStatusException
     * @throws StockHasNotPriceException
     * @throws TradeHasUnknownTypeException
     * @todo покрыть тестами
     * @todo объединить в один метод
     */
    public function calculatePaperResult(Trade $trade): float
    {
        if (Trade::STATUS_CLOSE === $trade->getStatus()) {
            throw new TradeHasCloseStatusException();
        }
        if (empty($trade->getStock()->getPrice())) {
            throw new StockHasNotPriceException();
        }

        if (Trade::TYPE_LONG === $trade->getType()) {
            return ($trade->getStock()->getPrice() - $trade->getOpenPrice()) * $trade->getLots() * $trade->getStock()->getLotSize();
        }
        if (Trade::TYPE_SHORT === $trade->getType()) {
            return ($trade->getOpenPrice() - $trade->getStock()->getPrice()) * $trade->getLots() * $trade->getStock()->getLotSize();
        }

        throw new TradeHasUnknownTypeException();
    }
}
