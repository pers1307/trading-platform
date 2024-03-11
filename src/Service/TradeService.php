<?php

namespace App\Service;

use App\Entity\Trade;
use App\Exception\StockHasNotPriceException;
use App\Exception\TradeHasNotClosePriceException;
use App\Exception\TradeHasUnknownStatusException;
use App\Exception\TradeHasUnknownTypeException;

class TradeService
{
    /**
     * @throws StockHasNotPriceException
     * @throws TradeHasNotClosePriceException
     * @throws TradeHasUnknownStatusException
     * @throws TradeHasUnknownTypeException
     */
    public function calculateResult(Trade $trade): float
    {
        $price = $this->getPriceForResult($trade);

        if (Trade::TYPE_LONG === $trade->getType()) {
            return ($price - $trade->getOpenPrice()) * $trade->getLots() * $trade->getStock()->getLotSize();
        }
        if (Trade::TYPE_SHORT === $trade->getType()) {
            return ($trade->getOpenPrice() - $price) * $trade->getLots() * $trade->getStock()->getLotSize();
        }

        throw new TradeHasUnknownTypeException();
    }

    /**
     * @throws StockHasNotPriceException
     * @throws TradeHasNotClosePriceException
     * @throws TradeHasUnknownStatusException
     */
    private function getPriceForResult(Trade $trade): float
    {
        if (Trade::STATUS_OPEN === $trade->getStatus()) {
            if (empty($trade->getStock()->getPrice())) {
                throw new StockHasNotPriceException();
            }

            return $trade->getStock()->getPrice();
        } elseif (Trade::STATUS_CLOSE === $trade->getStatus()) {
            if (empty($trade->getClosePrice())) {
                throw new TradeHasNotClosePriceException();
            }

            return $trade->getClosePrice();
        }

        throw new TradeHasUnknownStatusException();
    }
}
