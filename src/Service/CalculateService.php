<?php

namespace App\Service;

use App\Entity\RiskProfile;
use App\Entity\Stock;
use App\Entity\Trade;

class CalculateService
{
    public function calculatePersentByDeposit(RiskProfile $riskProfile, Stock $stock, int $lots): float
    {
        $positionCost = $stock->getPrice() * $lots * $stock->getLotSize();
        $depositPercent = ($positionCost / $riskProfile->getBalance()) * 100;

        return round($depositPercent, 2, PHP_ROUND_HALF_DOWN);
    }

    public function calculateLotsByDeposit(RiskProfile $riskProfile, Trade|Stock $source): int
    {
        if ($source instanceof Trade) {
            $trade = $source;
            return $this->calculateLotsByDepositFormula(
                $riskProfile->getPersent(),
                $riskProfile->getBalance(),
                $trade->getOpenPrice(),
                $trade->getStock()->getLotSize()
            );
        }

        $stock = $source;
        return $this->calculateLotsByDepositFormula(
            $riskProfile->getPersent(),
            $riskProfile->getBalance(),
            $stock->getPrice(),
            $stock->getLotSize()
        );
    }

    public function calculatePersentByTrade(RiskProfile $riskProfile, Trade $trade): float
    {
        $loss = abs($trade->getOpenPrice() - $trade->getStopLoss()) * $trade->getLots() * $trade->getStock()->getLotSize();
        $risk = (100 / $riskProfile->getBalance()) * $loss;

        return round($risk, 2, PHP_ROUND_HALF_DOWN);
    }

    public function calculateLotsByTrade(RiskProfile $riskProfile, Trade $trade): int
    {
        $lots = ($riskProfile->getPersent() * $riskProfile->getBalance())
            / (100 * abs($trade->getOpenPrice() - $trade->getStopLoss()) * $trade->getStock()->getLotSize());

        return round($lots, 0, PHP_ROUND_HALF_DOWN);
    }

    private function calculateLotsByDepositFormula(float $persent, float $balance, float $price, int $lotSize): int
    {
        $lots = $persent * $balance / (100 * $price * $lotSize);
        return round($lots, 0, PHP_ROUND_HALF_DOWN);
    }
}
