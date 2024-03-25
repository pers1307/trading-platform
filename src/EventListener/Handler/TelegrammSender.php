<?php

namespace App\EventListener\Handler;

use App\Event\NotificationEvent;
use App\Fabric\TelegrammBotApiFabric;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;

final class TelegrammSender implements SenderInterface
{
    public function __construct(
        private readonly string $telegrammBotToken,
        private readonly int $telegrammBotDialog,
        private readonly TelegrammBotApiFabric $telegrammBotApiFabric
    ) {
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function send(NotificationEvent $event): void
    {
        $message = "{$event->getTitle()}\n{$event->getText()}";
        
        $bot = $this->telegrammBotApiFabric->getBotApi($this->telegrammBotToken);
        $bot->sendMessage($this->telegrammBotDialog, $message);
    }
}
