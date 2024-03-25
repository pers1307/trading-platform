<?php

namespace App\Service;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @todo покрыть тестами
 */
class NotificationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @return Notification[]
     */
    public function findAll(): array
    {
        $notificationRepository = $this->entityManager->getRepository(Notification::class);
        return $notificationRepository->findLastFive();
    }

    public function setViewForAll(): void
    {
        $notificationRepository = $this->entityManager->getRepository(Notification::class);
        $notificationRepository->updateAllSetViewed();
    }
}
