<?php

namespace App\Service;

use App\Dto\Notification;
use App\Dto\OpenTradeNotifications;

class OpenTradeNotificationService
{
    public function merge(OpenTradeNotifications $openTradeNotifications): OpenTradeNotifications
    {
        if (
            empty($openTradeNotifications->getNotifications())
            || count($openTradeNotifications->getNotifications()) === 1
        ) {
            return $openTradeNotifications;
        }
        
        $text = '';
        foreach ($openTradeNotifications->getNotifications() as $key => $notification) {
            if ($key != 0) {
                $text .= "\n\n";
            }
            $text .= "{$notification->getTitle()}\n{$notification->getText()}";
        }

        return new OpenTradeNotifications([new Notification("", $text)], $openTradeNotifications->getCloseWarningTrades());
    }
}
