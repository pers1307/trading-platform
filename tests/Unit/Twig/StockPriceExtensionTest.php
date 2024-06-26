<?php

namespace App\Tests\Unit\Twig;

use App\Twig\StockPriceExtension;
use PHPUnit\Framework\TestCase;

class StockPriceExtensionTest extends TestCase
{
    private StockPriceExtension $stockPriceExtension;

    public function setUp(): void
    {
        $this->stockPriceExtension = new StockPriceExtension();
    }

    public function tearDown(): void
    {
        unset($this->stockPriceExtension);
    }

    /**
     * @covers       \App\Twig\StockPriceExtension::formatStockPrice
     * @dataProvider provider
     */
    public function testBaseFormatStockPriceBehavior(?float $price, float $minStep, string $expected, string $message)
    {
        $this->assertSame(
            $expected,
            $this->stockPriceExtension->formatStockPrice($price, $minStep),
            $message
        );
    }

    private function provider(): array
    {
        return [
            [10000, 50.0, '10 000', 'Шаг цены 50'],
            [10000.0, 20.0, '10 000', 'Шаг цены 20'],
            [10000.0, 10.0, '10 000', 'Шаг цены 10'],
            [10000, 2.0, '10 000', 'Шаг цены 2'],
            [10000, 1.0, '10 000', 'Шаг цены 1'],
            [10000, 0.1, '10 000.0', 'Шаг цены 0.1'],
            [10000.5, 0.1, '10 000.5', 'Шаг цены 0.1'],
            [10000, 0.2, '10 000.0', 'Шаг цены 0.2'],
            [10000.6, 0.2, '10 000.6', 'Шаг цены 0.2'],
            [10000, 0.01, '10 000.00', 'Шаг цены 0.01'],
            [10000.5, 0.01, '10 000.50', 'Шаг цены 0.01'],
            [10000.05, 0.01, '10 000.05', 'Шаг цены 0.01'],
            [16.76, 0.02, '16.76', 'Шаг цены 0.02'],
            [591.3, 0.05, '591.30', 'Шаг цены 0.05'],
            [67.4, 0.05, '67.40', 'Шаг цены 0.05'],
            [321.35, 0.05, '321.35', 'Шаг цены 0.05'],

            [10000, 0.001, '10 000.000', 'Шаг цены 0.001'],
            [10000.5, 0.001, '10 000.500', 'Шаг цены 0.001'],
            [10000.51, 0.001, '10 000.510', 'Шаг цены 0.001'],
            [10000.511, 0.001, '10 000.511', 'Шаг цены 0.001'],
            [10000.001, 0.001, '10 000.001', 'Шаг цены 0.001'],
            [10000.0001, 0.0001, '10 000.0001', 'Шаг цены 0.0001'],
            [10000.00001, 0.00001, '10 000.00001', 'Шаг цены 0.00001'],
            [0.023, 0.000005, '0.023000', 'Шаг цены 0.000005'],
            [0.0235, 0.000005, '0.023500', 'Шаг цены 0.000005'],
            [0.02350, 0.000005, '0.023500', 'Шаг цены 0.000005'],
            [0.023505, 0.000005, '0.023505', 'Шаг цены 0.000005'],

            [null, 1.0, '', 'При цене null выдаем пустую строку'],
            [86100.0, 200.0, '86 100 ⚠️', 'Цена 86200 не делится кратно на шаг цены'],
            [37010.0, 20.0, '37 010 ⚠️', 'Цена 37010 не делится кратно на шаг цены'],
            [2295.0, 2.0, '2 295 ⚠️', 'Цена не является четной'],
            [0.025, 1.0, '0.025 ⚠️', 'Цена меньше, чем шаг'],
            [640.3, 0.2, '640.3 ⚠️', 'Цена не является четной'],
            [654.4, 0.5, '654.4 ⚠️', 'Цена 654.4 не соответствует шагу цены 0.5'],
            [654.04, 0.05, '654.04 ⚠️', 'Цена 654.04 не соответствует шагу цены 0.05'],
            [654.001, 0.002, '654.001 ⚠️', 'Цена 654.001 не соответствует шагу цены 0.002'],
        ];
    }
}
