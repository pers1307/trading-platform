<?php

namespace App\Tests\Unit;

use App\Dto\Graph;
use App\Service\GraphService;
use PHPUnit\Framework\TestCase;

class GraphServiceTest extends TestCase
{
    private GraphService $graphService;

    public function setUp(): void
    {
        $this->graphService = new GraphService();
    }

    public function tearDown(): void
    {
        unset($this->graphService);
    }

    /**
     * @covers       \App\Service\GraphService::format
     */
    public function testFormat()
    {
        $input = [100.0, 200.01, 305.50, 1000];

        $result = $this->graphService->format(
            $input,
            static fn(int $key, float $item) => $key,
            static fn(int $key, float $item) => (string)$item
        );

        $this->assertEquals(new Graph('{"labels":[0,1,2,3],"values":["100","200.01","305.5","1000"]}'), $result);
    }
}
