<?php

namespace App\Widget;

use App\Entity\Notification;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @todo покрыть функциональными тестами
 */
class NotificationWidget extends AbstractController
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {
    }

    public function show(): Response
    {
        $notifications = $this->notificationService->findAll();
        $countNotifications = count($notifications);

        /**
         * @todo перенести все это в сервис и покрыть тестами
         */
        $newNotifications = array_filter($notifications, static fn(Notification $notification) => !$notification->getViewed());
        $countNewNotifications = count($newNotifications);

        return $this->render('widgets/notification/show.html.twig', [
            'notifications' => $notifications,
            'countNewNotifications' => $countNewNotifications,
            'countNotifications' => $countNotifications,
        ]);
    }

    #[Route('/notification/viewed', name: 'notification_widget_viewed', methods: ['POST'])]
    public function viewed(): Response
    {
        $this->notificationService->setViewForAll();

        return new Response();
    }
}
