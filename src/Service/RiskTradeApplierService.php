<?php

namespace App\Service;

use App\Dto\RiskTrades;
use App\Entity\Trade;
use App\Entity\TradeRiskWarning;
use App\Event\NotificationEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RiskTradeApplierService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function apply(RiskTrades $riskTrades): void
    {
        foreach ($riskTrades->getTrades() as $riskTrade) {
            $this->createTradeRiskWarning($riskTrade);
        }
        $this->entityManager->flush();

        foreach ($riskTrades->getRiskTradeNotifications() as $riskTradeNotification) {
            $notificationEvent = new NotificationEvent(
                $riskTradeNotification->getTitle(),
                $riskTradeNotification->getText(),
            );
            $this->eventDispatcher->dispatch($notificationEvent);
        }
    }

    private function createTradeRiskWarning(Trade $trade): void
    {
        $tradeRiskWarning = new TradeRiskWarning();
        $tradeRiskWarning->setTrade($trade);

        $this->entityManager->persist($tradeRiskWarning);
    }
}
