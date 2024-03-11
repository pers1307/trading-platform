<?php

namespace Unit\Twig;

use App\Entity\Trade;
use App\Twig\TradeExtension;
use PHPUnit\Framework\TestCase;

class TradeExtensionTest extends TestCase
{
    private TradeExtension $tradeExtension;

    public function setUp(): void
    {
        $this->tradeExtension = new TradeExtension();
    }

    public function tearDown(): void
    {
        unset($this->tradeExtension);
    }

    /**
     * @covers       \App\Twig\TradeExtension::formatTradeStatus
     * @dataProvider providerTradeStatus
     */
    public function testFormatTradeStatus(string $input, string $expected, string $message)
    {
        $this->assertSame(
            $expected,
            $this->tradeExtension->formatTradeStatus($input),
            $message
        );
    }

    /**
     * @covers       \App\Twig\TradeExtension::formatTradeStatus
     * @dataProvider providerTradeType
     */
    public function formatTradeType(string $input, string $expected, string $message)
    {
        $this->assertSame(
            $expected,
            $this->tradeExtension->formatTradeType($input),
            $message
        );
    }

    private function providerTradeType(): array
    {
        return [
            [Trade::TYPE_LONG, '<span class="badge bg-success">Long</span>', 'Длинная позиция'],
            [Trade::TYPE_SHORT, '<span class="badge bg-danger">Short</span>', 'Короткая позиция'],
            ['Unknown', '<span class="badge bg-warning text-dark">Unknown</span>', 'Неизвестная позиция'],
        ];
    }

    private function providerTradeStatus(): array
    {
        return [
            [Trade::STATUS_OPEN, '<span class="badge bg-info text-dark">Open</span>', 'Открытая позиция'],
            [Trade::STATUS_CLOSE, '<span class="badge bg-secondary">Close</span>', 'Закрытая позиция'],
            ['Unknown', '<span class="badge bg-warning text-dark">Unknown</span>', 'Неизвестная позиция'],
        ];
    }
}
