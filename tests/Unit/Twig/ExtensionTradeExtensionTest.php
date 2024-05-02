<?php

namespace App\Tests\Unit\Twig;

use App\Entity\Trade;
use App\Twig\ExtensionTradeExtension;
use PHPUnit\Framework\TestCase;

class ExtensionTradeExtensionTest extends TestCase
{
    private ExtensionTradeExtension $extensionTradeExtension;

    public function setUp(): void
    {
        $this->extensionTradeExtension = new ExtensionTradeExtension();
    }

    public function tearDown(): void
    {
        unset($this->extensionTradeExtension);
    }

    /**
     * @covers       \App\Twig\ExtensionTradeExtension::formatTradeResult
     * @dataProvider provider
     */
    public function testFormatTradeStatus(?float $input, string $expected, string $message)
    {
        $this->assertSame(
            $expected,
            $this->extensionTradeExtension->formatTradeResult($input),
            $message
        );
    }

    private function provider(): array
    {
        return [
            [
                0,
                '0.00',
                'Нулевое изменение цены',
            ],
            [
                null,
                '',
                'Изменение цены равно null',
            ],
            [
                100,
                '100.00',
                'Не нулевое изменение цены',
            ],
        ];
    }
}
