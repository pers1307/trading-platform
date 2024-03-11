<?php

namespace Unit;

use App\Dto\ExtensionTrade;
use App\Entity\Trade;
use App\Service\CumulativeTotalService;
use Exception;
use PHPUnit\Framework\TestCase;

class CumulativeTotalServiceTest extends TestCase
{
    private CumulativeTotalService $cumulativeTotalService;

    public function setUp(): void
    {
        $this->cumulativeTotalService = new CumulativeTotalService();
    }

    public function tearDown(): void
    {
        unset($this->cumulativeTotalService);
    }

    /**
     * @covers       \App\Service\CumulativeTotalService::calculate
     * @throws Exception
     */
    public function testCalculate()
    {
        $input = $this->getExtensionTrades();

        $actual = $this->cumulativeTotalService->calculate($input);

        $this->assertEquals([100, 300, 600], $actual);
    }

    private function getExtensionTrades(): array
    {
        return [
            new ExtensionTrade(new Trade(), 100),
            new ExtensionTrade(new Trade(), 200),
            new ExtensionTrade(new Trade(), 300),
        ];
    }
}
