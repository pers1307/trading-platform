<?php

namespace App\Service;

use App\Dto\ActiveTradesWithRisks;
use App\Dto\Notification;
use App\Dto\RiskTrades;
use App\Entity\RiskProfile;
use App\Entity\Trade;

class RiskTradeService
{
    public function __construct(
        private readonly CalculateService $calculateService,
    ) {
    }

    public function check(ActiveTradesWithRisks $activeTradesWithRisks): RiskTrades
    {
        $riskTrades = [];
        $riskTradeNotifications = [];
        foreach ($activeTradesWithRisks->getTrades() as $trade) {
            if ($trade->isExistsTradeRiskWarning()) {
                continue;
            }

            $riskProfile = $this->getRiskProfileByTrade($trade, $activeTradesWithRisks->getIndexRiskProfiles());
            $lots = $this->calculateLots($trade, $riskProfile);

            if ($lots < $trade->getLots()) {
                $riskTrades[] = $trade;
                $riskTradeNotifications[] = $this->createRiskTradeNotification($trade, $riskProfile, $lots);
            }
        }

        return new RiskTrades(
            $riskTrades,
            $riskTradeNotifications
        );
    }

    /**
     * @param RiskProfile[] $indexRiskProfile
     */
    private function getRiskProfileByTrade(Trade $trade, array $indexRiskProfile): RiskProfile
    {
        $key = "{$trade->getAccaunt()->getId()}-{$trade->getStrategy()->getId()}";
        return $indexRiskProfile[$key];
    }

    private function calculateLots(Trade $trade, RiskProfile $riskProfile): int
    {
        if (RiskProfile::TYPE_DEPOSIT === $riskProfile->getType()) {
            return $this->calculateService->calculateLotsByDeposit($riskProfile, $trade);
        } elseif (RiskProfile::TYPE_TRADE === $riskProfile->getType()) {
            return $this->calculateService->calculateLotsByTrade($riskProfile, $trade);
        }

        return 0;
    }

    private function createRiskTradeNotification(Trade $trade, RiskProfile $riskProfile, int $recommendedLots): Notification
    {
        $type = ucfirst($trade->getType());
        $balance = number_format($riskProfile->getBalance(), 0, '.', ' ');
        if (RiskProfile::TYPE_DEPOSIT === $riskProfile->getType()) {
            $text = "{$trade->getAccaunt()->getTitle()}. {$trade->getStrategy()->getTitle()}. {$trade->getStock()->getSecId()}. $type."
                . "\n"
                . "При риске {$riskProfile->getPersent()}% от депозита {$balance}."
                . "\n"
                . "Рассчет: $recommendedLots лотов. Факт: {$trade->getLots()}";
        } elseif (RiskProfile::TYPE_TRADE === $riskProfile->getType()) {
            $text = "{$trade->getAccaunt()->getTitle()}. {$trade->getStrategy()->getTitle()}. {$trade->getStock()->getSecId()}. $type."
                . "\n"
                . "При риске {$riskProfile->getPersent()}% на сделку от депозита {$balance}."
                . "\n"
                . "Рассчет: $recommendedLots лотов. Факт: {$trade->getLots()}";
        } else {
            $text = "{$trade->getAccaunt()->getTitle()}. {$trade->getStrategy()->getTitle()}. {$trade->getStock()->getSecId()}. $type.\n"
                . "Рассчет: $recommendedLots лотов. Факт: {$trade->getLots()}";
        }

        return new Notification(
            'Нарушение риск-менеджмента!',
            $text
        );
    }
}
