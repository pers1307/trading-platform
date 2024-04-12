<?php

namespace App\Dto;

class OpenTradeNotifications
{
    public function __construct(
        private readonly array $notifications,
    ) {
    }

    /**
     * @return Notification[]
     */
    public function getNotifications(): array
    {
        return $this->notifications;
    }
}
