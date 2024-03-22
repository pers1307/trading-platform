<?php

namespace Unit\Twig;

use App\Entity\RiskProfile;
use App\Twig\RiskProfileExtension;
use PHPUnit\Framework\TestCase;

class RiskProfileExtensionTest extends TestCase
{
    private RiskProfileExtension $riskProfileExtension;

    public function setUp(): void
    {
        $this->riskProfileExtension = new RiskProfileExtension();
    }

    public function tearDown(): void
    {
        unset($this->riskProfileExtension);
    }

    /**
     * @covers       \App\Twig\RiskProfileExtension::formatType
     * @dataProvider providerTradeType
     */
    public function testFormatType(string $input, string $expected, string $message)
    {
        $this->assertSame(
            $expected,
            $this->riskProfileExtension->formatType($input),
            $message
        );
    }

    private function providerTradeType(): array
    {
        return [
            [RiskProfile::TYPE_DEPOSIT, '<span class="badge bg-primary text-dark">Процент от депозита</span>', 'Процент от депозита'],
            [RiskProfile::TYPE_TRADE, '<span class="badge bg-danger text-dark">Процент от риска в сделке</span>', 'Процент от риска в сделке'],
            ['Unknown', '<span class="badge bg-warning text-dark">Unknown</span>', 'Неизвестный тип расчета'],
        ];
    }
}
