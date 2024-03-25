<?php

namespace App\EventListener\Handler;

use App\Event\NotificationEvent;

interface SenderInterface
{
    public function send(NotificationEvent $event): void;
}
