<?php

namespace App\Service;

use App\Dto\OpenTradeNotifications;
use App\Entity\Trade;
use App\Entity\TradeCloseWarning;
use App\Event\NotificationEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OpenTradeApplierService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function apply(OpenTradeNotifications $openTradeNotifications): void
    {
        foreach ($openTradeNotifications->getCloseWarningTrades() as $closeTrade) {
            $this->createTradeCloseWarning($closeTrade);
        }
        $this->entityManager->flush();

        foreach ($openTradeNotifications->getNotifications() as $notification) {
            $notificationEvent = new NotificationEvent(
                $notification->getTitle(),
                $notification->getText(),
            );
            $this->eventDispatcher->dispatch($notificationEvent);
        }
    }

    private function createTradeCloseWarning(Trade $trade): void
    {
        $tradeCloseWarning = new TradeCloseWarning();
        $tradeCloseWarning->setTrade($trade);

        $this->entityManager->persist($tradeCloseWarning);
    }
}
