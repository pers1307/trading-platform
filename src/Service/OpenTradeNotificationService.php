<?php

namespace App\Service;

use App\Dto\Notification;
use App\Dto\OpenTradeNotifications;

class OpenTradeNotificationService
{
    public function merge(OpenTradeNotifications $openTradeNotifications): OpenTradeNotifications
    {
        if (empty($openTradeNotifications->getNotifications())) {
            return $openTradeNotifications;
        }

        $title = '';
        $text = '';
        foreach ($openTradeNotifications->getNotifications() as $key => $notification) {
            if ($key === 0) {
                $title = $notification->getTitle();
            }

            if ($key != 0) {
                $text .= '\n\n';
            }
            $text .= $notification->getText();
        }

        return new OpenTradeNotifications([new Notification($title, $text)]);
    }
}
