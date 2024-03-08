<?php

namespace App\Service;

use App\Dto\ExtensionTrade;
use App\Entity\Trade;
use App\Exception\TradeHasNotClosePriceException;
use App\Exception\TradeHasOpenStatusException;
use App\Exception\TradeHasUnknownTypeException;

/**
 * @todo: нет интеграционных тестов
 */
class ExtensionTradeService
{
    public function __construct(
        private readonly TradeService $tradeService,
    ) {
    }

    /**
     * @param Trade[] $trades
     * @return ExtensionTrade[]
     * @throws TradeHasNotClosePriceException
     * @throws TradeHasOpenStatusException
     * @throws TradeHasUnknownTypeException
     */
    public function convertTradesToExtensionTrades(array $trades): array
    {
        $extensionTrades = [];
        $cumulativeTotal = 0.0;
        foreach ($trades as $trade) {
            // todo: временно, зарефакторить
            if (Trade::STATUS_CLOSE === $trade->getStatus()) {
                $tradeResult = $this->tradeService->calculateResult($trade);
            } else {
                $tradeResult = $this->tradeService->calculatePaperResult($trade);
            }
            // @todo: это перенести в сервис статистики. Это не информация, которую мы извлекаем из трейда
            $cumulativeTotal = round($cumulativeTotal + $tradeResult, 2);
            $extensionTrades[] = new ExtensionTrade($trade, $tradeResult, $cumulativeTotal);
        }

        return $extensionTrades;
    }
}
