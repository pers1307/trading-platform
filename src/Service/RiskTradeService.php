<?php

namespace App\Service;

use App\Dto\ActiveTradesWithRisks;
use App\Dto\RiskTradeNotification;
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
            return $this->calculateService->calculateLotsByDepositPersentForTrade($riskProfile, $trade);
        } elseif (RiskProfile::TYPE_TRADE === $riskProfile->getType()) {
            return $this->calculateService->calculateLotsByTradePersent($riskProfile, $trade);
        }

        return 0;
    }

    private function createRiskTradeNotification(Trade $trade, RiskProfile $riskProfile, int $recommendedLots): RiskTradeNotification
    {
        $type = ucfirst($trade->getType());
        $text = "{$trade->getAccaunt()->getTitle()}. {$trade->getStrategy()->getTitle()}. {$trade->getStock()->getSecId()}. $type.\nРассчет: $recommendedLots лотов. Факт: {$trade->getLots()}";

        return new RiskTradeNotification(
            'Нарушение риск-менеджмента!',
            $text
        );

        // @todo: разные нотификации в зависимости от риск профиля

        //        if (RiskProfile::TYPE_DEPOSIT === $riskProfile->getType()) {
        //            return $this->calculateService->calculateLotsByDepositPersent($riskProfile, $trade->getStock());
        //        } elseif (RiskProfile::TYPE_TRADE === $riskProfile->getType()) {
        //            return $this->calculateService->calculateLotsByTradePersent($riskProfile, $trade);
        //        }
        //
        //        return 0;
    }
}
