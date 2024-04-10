<?php

namespace App\Service;

use App\Entity\RiskProfile;
use App\Entity\Stock;
use App\Entity\Trade;

/**
 * @todo зарефакторить этот сервис
 */
class CalculateService
{
    public function __construct()
    {
    }

    //    public function calculate(RiskProfile $riskProfile, Stock $stock, ?float $stopLoss = null): int
    //    {
    //        if (RiskProfile::TYPE_DEPOSIT === $riskProfile->getType()) {
    //            $this->calculateByDeposit($riskProfile, $stock);
    //        }
    //
    //        /**
    //         * Обновить значение цены
    //         */
    //
    //        /**
    //         * Высчитать по риск профилю объем
    //         */
    //    }

    public function calculateByDepositPersent(RiskProfile $riskProfile, Stock $stock, int $lots): float
    {
        $positionCost = $stock->getPrice() * $lots * $stock->getLotSize();
        $depositPercent = ($positionCost / $riskProfile->getBalance()) * 100;

        return round($depositPercent, 2, PHP_ROUND_HALF_EVEN);
    }

    /**
     * @todo зарефакторить эти методы
     */
    public function calculateLotsByDepositPersentForTrade(RiskProfile $riskProfile, Trade $trade): int
    {
        $lots = $riskProfile->getPersent() * $riskProfile->getBalance() / (100 * $trade->getOpenPrice() * $trade->getStock()->getLotSize());

        return round($lots);
    }

    // Поможет при рассчете
    public function calculateLotsByDepositPersent(RiskProfile $riskProfile, Stock $stock): int
    {
        $lots = $riskProfile->getPersent() * $riskProfile->getBalance() / (100 * $stock->getPrice() * $stock->getLotSize());

        return round($lots);
    }

    public function calculateByTradePersent(RiskProfile $riskProfile, Trade $trade): float
    {
        $loss = abs($trade->getOpenPrice() - $trade->getStopLoss()) * $trade->getLots() * $trade->getStock()->getLotSize();
        $risk = (100 / $riskProfile->getBalance()) * $loss;

        return round($risk, 2, PHP_ROUND_HALF_EVEN);
    }

    public function calculateLotsByTradePersent(RiskProfile $riskProfile, Trade $trade): int
    {
        $lots = ($riskProfile->getPersent() * $riskProfile->getBalance())
            / (100 * abs($trade->getOpenPrice() - $trade->getStopLoss()) * $trade->getStock()->getLotSize());

        return round($lots);
    }
}
