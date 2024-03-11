<?php

namespace App\Service;

use App\Dto\ExtensionTrade;
use App\Entity\Trade;
use App\Exception\StockHasNotPriceException;
use App\Exception\TradeHasNotClosePriceException;
use App\Exception\TradeHasUnknownStatusException;
use App\Exception\TradeHasUnknownTypeException;

class ExtensionTradeService
{
    public function __construct(
        private readonly TradeService $tradeService,
    ) {
    }

    /**
     * @param Trade[] $trades
     * @throws TradeHasNotClosePriceException
     * @throws TradeHasUnknownTypeException
     * @throws StockHasNotPriceException
     * @throws TradeHasUnknownStatusException
     */
    public function convertTradesToExtensionTrades(array $trades): array
    {
        $extensionTrades = [];
        foreach ($trades as $trade) {
            $tradeResult = $this->tradeService->calculateResult($trade);
            $extensionTrades[] = new ExtensionTrade($trade, $tradeResult);
        }

        return $extensionTrades;
    }
}
