<?php

namespace Integration;

use App\Event\NotificationEvent;
use App\EventListener\Handler\TelegrammSender;
use App\EventListener\NotificationEventListener;
use App\Fabric\TelegrammBotApiFabric;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use TelegramBot\Api\BotApi;

/**
 * @todo подрефакторить, когда появиться логика отображения нотификаций
 */
class NotificationEventListenerTest extends KernelTestCase
{
    public function testTelegrammSender(): void
    {
        $title = 'title';
        $text = 'text';
        $notificationEvent = new NotificationEvent($title, $text);

        $botApiMock = $this->getMockBuilder(BotApi::class)
            ->disableOriginalConstructor()
            ->getMock();
        $botApiMock->expects($this->once())
            ->method('sendMessage')
            ->with(123, "title\ntext");

        $telegrammBotApiFabricMock = $this->getMockBuilder(TelegrammBotApiFabric::class)
            ->getMock();
        $telegrammBotApiFabricMock
            ->expects($this->once())
            ->method('getBotApi')
            ->willReturn($botApiMock);

        $telegrammSender = new TelegrammSender('123', 123, $telegrammBotApiFabricMock);

        $notificationEventListener = new NotificationEventListener();
        $notificationEventListener->addSender($telegrammSender);

        $notificationEventListener($notificationEvent);
    }

    public function testDataBaseSender(): void
    {
        $title = 'title';
        $text = 'text';
        $notificationEvent = new NotificationEvent($title, $text);

        $container = static::getContainer();
        $dataBaseSender = $container->get('db_sender');

        $notificationEventListener = new NotificationEventListener();
        $notificationEventListener->addSender($dataBaseSender);

        $notificationEventListener($notificationEvent);

        $notificationRepository = $container->get(NotificationRepository::class);
        $notifications = $notificationRepository->findAll();
        $this->assertCount(1, $notifications);
    }
}
