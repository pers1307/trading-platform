<?php

namespace App\Tests\Unit;

use App\Dto\Notification;
use App\Dto\OpenTradeNotifications;
use App\Service\OpenTradeNotificationService;
use PHPUnit\Framework\TestCase;

class OpenTradeNotificationServiceTest extends TestCase
{
    private OpenTradeNotificationService $openTradeNotificationService;

    public function setUp(): void
    {
        $this->openTradeNotificationService = new OpenTradeNotificationService();
    }

    public function tearDown(): void
    {
        unset($this->openTradeNotificationService);
    }

    /**
     * @covers       \App\Service\OpenTradeNotificationService::merge
     * @dataProvider provider
     */
    public function testMerge(OpenTradeNotifications $input, OpenTradeNotifications $expected, string $message)
    {
        $this->assertEquals(
            $expected,
            $this->openTradeNotificationService->merge($input),
            $message
        );
    }

    /**
     * @throws \Exception
     */
    private function provider(): array
    {
        return [
            [
                new OpenTradeNotifications([], []),
                new OpenTradeNotifications([], []),
                'Нет нотификации',
            ],
            [
                new OpenTradeNotifications([
                    new Notification('Hello', 'Hello yellow!'),
                ], []),
                new OpenTradeNotifications([
                    new Notification('Hello', 'Hello yellow!'),
                ], []),
                'Одна нотификация. Мержа не происходит',
            ],
            [
                new OpenTradeNotifications([
                    new Notification('Hello', 'Hello yellow!'),
                    new Notification('Hello!', 'Hello green!'),
                ], []),
                new OpenTradeNotifications([
                    new Notification('', "Hello\nHello yellow!\n\nHello!\nHello green!"),
                ], []),
                'Две нотификации объединяются в одну',
            ],
        ];
    }
}
