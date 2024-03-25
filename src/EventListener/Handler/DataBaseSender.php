<?php

namespace App\EventListener\Handler;

use App\Entity\Notification;
use App\Event\NotificationEvent;
use Doctrine\ORM\EntityManagerInterface;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;

final class DataBaseSender implements SenderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function send(NotificationEvent $event): void
    {
        $notification = new Notification();
        $notification->setTitle($event->getTitle());
        $notification->setText($event->getText());

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
}
