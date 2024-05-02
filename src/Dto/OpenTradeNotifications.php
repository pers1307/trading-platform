<?php

namespace App\Dto;

use App\Entity\Trade;

class OpenTradeNotifications
{
    public function __construct(
        private readonly array $notifications,
        private readonly array $closeWarningTrades,
    ) {
    }

    /**
     * @return Notification[]
     */
    public function getNotifications(): array
    {
        return $this->notifications;
    }

    /**
     * @return Trade[]
     */
    public function getCloseWarningTrades(): array
    {
        return $this->closeWarningTrades;
    }
}
