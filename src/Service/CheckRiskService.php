<?php

namespace App\Service;

use App\Entity\RiskProfile;
use App\Entity\Trade;
use App\Entity\TradeRiskWarning;
use App\Event\NotificationEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @todo покрыть интеграцилнными тестами
 * @todo рефакторинг
 * Возвращает команду создания побочного эффекта, которую он хочет выполнить
 * Разделение бизнес логики и побочных эффектов
 */
class CheckRiskService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RiskProfileService $riskProfileService,
        private readonly CalculateService $calculateService,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @param RiskProfile[] $indexRiskProfile
     */
    private function getRiskProfileByTrade(Trade $trade, array $indexRiskProfile): RiskProfile
    {
        $key = "{$trade->getAccaunt()->getId()}-{$trade->getStrategy()->getId()}";
        return $indexRiskProfile[$key];
    }

    private function createTradeRiskWarning(Trade $trade): void
    {
        $tradeRiskWarning = new TradeRiskWarning();
        $tradeRiskWarning->setTrade($trade);

        $this->entityManager->persist($tradeRiskWarning);
        $this->entityManager->flush();
    }

    /**
     * @todo покрыть тестами
     * @todo рефакторить!
     */
    public function checkAllOpenTrade(): void
    {
        $tradeRepository = $this->entityManager->getRepository(Trade::class);
        $activeTrades = $tradeRepository->findAllActive();
        $indexRiskProfile = $this->riskProfileService->getIndexAll();

        foreach ($activeTrades as $activeTrade) {
            if ($activeTrade->isExistsTradeRiskWarning()) {
                continue;
            }

            $riskProfile = $this->getRiskProfileByTrade($activeTrade, $indexRiskProfile);

            if (RiskProfile::TYPE_DEPOSIT === $riskProfile->getType()) {
                $lots = $this->calculateService->calculateLotsByDepositPersent($riskProfile, $activeTrade->getStock());

                if ($lots < $activeTrade->getLots()) {
                    $this->createTradeRiskWarning($activeTrade);

                    $type = ucfirst($activeTrade->getType());
                    $text = "{$activeTrade->getAccaunt()->getTitle()}. {$activeTrade->getStrategy()->getTitle()}. {$activeTrade->getStock()->getSecId()}. $type.\nРассчет: $lots лотов. Факт: {$activeTrade->getLots()}";
                    $notificationEvent = new NotificationEvent('Нарушение риск-менеджмента!', $text);
                    $this->eventDispatcher->dispatch($notificationEvent);
                }
            } elseif (RiskProfile::TYPE_TRADE === $riskProfile->getType()) {
                $lots = $this->calculateService->calculateLotsByTradePersent($riskProfile, $activeTrade);

                if ($lots < $activeTrade->getLots()) {
                    $this->createTradeRiskWarning($activeTrade);

                    $type = ucfirst($activeTrade->getType());
                    $text = "{$activeTrade->getAccaunt()->getTitle()}. {$activeTrade->getStrategy()->getTitle()}. {$activeTrade->getStock()->getSecId()}. $type.\nРассчет: $lots лотов. Факт: {$activeTrade->getLots()}";
                    $notificationEvent = new NotificationEvent('Нарушение риск-менеджмента!', $text);
                    $this->eventDispatcher->dispatch($notificationEvent);
                }
            }
        }
    }
}
