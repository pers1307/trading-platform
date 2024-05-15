<?php

namespace App\Service;

use App\Dto\ExtensionTrade;
use App\Dto\Statistic\CountStatistic;
use App\Dto\Statistic\ExpectedStatistic;
use App\Dto\Statistic\Statistic;
use App\Dto\Statistic\TimeStatistic;
use Monolog\DateTimeImmutable;

/**
 * @todo актуализировать тесты
 * @todo добавить тесты под новые статистические данные
 */
class StatisticService
{
    private readonly DateTimeImmutable $nowDateTime;

    public function __construct(
        string $now
    ) {
        $this->nowDateTime = new DateTimeImmutable($now);
    }

    /**
     * @param ExtensionTrade[] $extensionTrades
     */
    public function calculate(array $extensionTrades): ?Statistic
    {
        if (empty($extensionTrades)) {
            return null;
        }

        $countStatistic = $this->calculateCountTrades($extensionTrades);
        $expectedStatistic = $this->calculateExpectedTrades($extensionTrades);
        $timeStatistic = $this->calculateAverageTimeTrades($extensionTrades);

        return new Statistic(
            $countStatistic,
            $expectedStatistic,
            $timeStatistic
        );
    }

    /**
     * @param ExtensionTrade[] $extensionTrades
     */
    public function calculateExpectedTrades(array $extensionTrades): ExpectedStatistic
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

        return new ExpectedStatistic(
            $expectedValue,
            $averageProfit,
            $averageLoss,
        );
    }

    /**
     * @param ExtensionTrade[] $extensionTrades
     */
    public function calculateCountTrades(array $extensionTrades): CountStatistic
    {
        $countProfitTrades = 0;
        $countLossTrades = 0;

        foreach ($extensionTrades as $extensionTrade) {
            if ($extensionTrade->getTradeResult() > 0) {
                ++$countProfitTrades;
            } else {
                ++$countLossTrades;
            }
        }

        $countTrades = count($extensionTrades);
        $persentProfitTrades = round(($countProfitTrades / $countTrades) * 100, 2);
        $persentLossTrades = round(($countLossTrades / $countTrades) * 100, 2);

        return new CountStatistic(
            $countTrades,
            $countProfitTrades,
            $countLossTrades,
            $persentProfitTrades,
            $persentLossTrades
        );
    }

    /**
     * @param ExtensionTrade[] $extensionTrades
     */
    public function calculateAverageTimeTrades(array $extensionTrades): TimeStatistic
    {
        $totalTimeIntervalInSecond = round(
            $this->nowDateTime->getTimestamp()
            - $extensionTrades[0]->getTrade()->getOpenDateTime()->getTimestamp()
        );

        $countProfitTrades = 0;
        $countLossTrades = 0;

        $totalProfitTradesInSecond = 0;
        $totalLossTradesInSecond = 0;
        $totalTradesInSecond = 0;

        foreach ($extensionTrades as $extensionTrade) {
            if (
                !empty($extensionTrade->getTrade()->getOpenDateTime())
                && !empty($extensionTrade->getTrade()->getCloseDateTime())
            ) {
                $dateIntervalInSecond = $extensionTrade->getTrade()->getCloseDateTime()->getTimestamp()
                    - $extensionTrade->getTrade()->getOpenDateTime()->getTimestamp();
            } else {
                continue;
            }

            if ($extensionTrade->getTradeResult() > 0) {
                ++$countProfitTrades;
                $totalProfitTradesInSecond += $dateIntervalInSecond;
            } else {
                ++$countLossTrades;
                $totalLossTradesInSecond += $dateIntervalInSecond;
            }

            $totalTradesInSecond += $dateIntervalInSecond;
        }

        $averageProfitTradeInSecond = round($totalProfitTradesInSecond / $countProfitTrades);
        $averageLossTradeInSecond = round($totalLossTradesInSecond / $countLossTrades);
        $averageTradeInSecond = round($totalTradesInSecond / ($countLossTrades + $countProfitTrades));

        return new TimeStatistic(
            $totalTimeIntervalInSecond,
            $averageProfitTradeInSecond,
            $averageLossTradeInSecond,
            $averageTradeInSecond
        );
    }
}
