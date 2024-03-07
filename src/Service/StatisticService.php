<?php

namespace App\Service;

use App\Dto\ExtensionTrade;
use App\Dto\ExtensionTradesCollection;

/**
 * @todo: нет интеграционных тестов
 */
class StatisticService
{
    public function __construct(
        private readonly GraphService $graphService,
    ) {
    }

    /**
     * @param ExtensionTrade[] $extensionTrades
     * @todo логично, что статистические данные, которые должны отдаваться в сервисе, это не коллекция а нечто свое
     * а коллекция должна родиться на уровень выше
     */
    public function calculate(array $extensionTrades): ExtensionTradesCollection
    {
        $countLossTrades = 0;
        $countProfitTrades = 0;
        $summaryProfit = 0;
        $summaryLoss = 0;

        foreach ($extensionTrades as $extensionTrade) {
            if ($extensionTrade->getTradeResult() > 0) {
                $summaryProfit += $extensionTrade->getTradeResult();
                ++$countProfitTrades;
            } else {
                $summaryLoss += $extensionTrade->getTradeResult();
                ++$countLossTrades;
            }
        }

        $countTrades = count($extensionTrades);
        $averageProfit = $summaryProfit / $countTrades;
        $averageLoss = $summaryLoss / $countTrades;
        $expectedValue = ($countProfitTrades / $countTrades) * $averageProfit + ($countLossTrades / $countTrades) * $averageLoss;

        // @todo: данные для графика это тоже некий сторонний объект дто должен быть, который будет правильно помещен в необходимую сущность
        $graphFormatData = $this->graphService->format(
            $extensionTrades,
            static fn(int $key, ExtensionTrade $extensionTrade) => $extensionTrade->getTrade()->getOpenDateTime()->format('Y-m-d'),
            static fn(int $key, ExtensionTrade $extensionTrade) => intval($extensionTrade->getCumulativeTotal()),
        );

        return new ExtensionTradesCollection(
            $extensionTrades,
            $countLossTrades,
            $countProfitTrades,
            $averageProfit,
            $averageLoss,
            $expectedValue,
            $graphFormatData
        );
    }
}
