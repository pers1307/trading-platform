<?php

namespace App\EventListener;

use App\Event\NotificationEvent;
use App\EventListener\Handler\SenderInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class NotificationEventListener
{
    private array $senders = [];

    public function addSender(SenderInterface $sender): void
    {
        $this->senders[] = $sender;
    }

    public function __invoke(NotificationEvent $event): void
    {
        foreach ($this->senders as $sender) {
            $sender->send($event);
        }
    }
}
