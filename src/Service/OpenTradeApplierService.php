<?php

namespace App\Service;

use App\Dto\OpenTradeNotifications;
use App\Event\NotificationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OpenTradeApplierService
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function apply(OpenTradeNotifications $openTradeNotifications): void
    {
        foreach ($openTradeNotifications->getNotifications() as $notification) {
            $notificationEvent = new NotificationEvent(
                $notification->getTitle(),
                $notification->getText(),
            );
            $this->eventDispatcher->dispatch($notificationEvent);
        }
    }
}
