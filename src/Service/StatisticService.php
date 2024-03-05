<?php

namespace App\Service;

use App\Dto\ExtensionTrade;
use App\Dto\StrategyStatistics;

class StatisticService
{
    /**
     * @param ExtensionTrade[] $extensionTrades
     */
    public function calculate(array $extensionTrades): StrategyStatistics
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

        return new StrategyStatistics(
            $extensionTrades,
            $countLossTrades,
            $countProfitTrades,
            $averageProfit,
            $averageLoss,
            $expectedValue
        );
    }
}
