<?php

namespace Unit;

use PHPUnit\Framework\TestCase;

class StatisticServiceTest extends TestCase
{
    /**
     * Подпихнуть сет из сделок, проверить, что считается правильно
     */

//    private GraphService $graphService;
//
//    public function setUp(): void
//    {
//        $this->graphService = new GraphService();
//    }
//
//    public function tearDown(): void
//    {
//        unset($this->graphService);
//    }
//
//    /**
//     * @covers       \App\Service\GraphService::format
//     */
//    public function testFormat()
//    {
//        $expected = '{"labels":[0,1,2,3],"values":["100","200.01","305.5","1000"]}';
//        $input = [100.0, 200.01, 305.50, 1000];
//
//        $this->assertSame(
//            $expected,
//            $this->graphService->format(
//                $input,
//                static fn(int $key, float $item) => $key,
//                static fn(int $key, float $item) => (string)$item
//            )
//        );
//    }
}
