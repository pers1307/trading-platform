<?php

namespace App\Tests\Integration;

use App\Service\AccauntService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AccauntServiceTest extends KernelTestCase
{
    public function testFindAllDb(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $accauntService = $container->get(AccauntService::class);
        $result = $accauntService->findAll();

        $this->assertCount(2, $result);
    }
}
