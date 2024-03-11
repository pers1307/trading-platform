<?php

namespace Integration;

use App\Service\DictionaryStockService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DictionaryStockServiceTest extends KernelTestCase
{
    public function testFindAllDb(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $dictionaryStockService = $container->get(DictionaryStockService::class);
        $result = $dictionaryStockService->findAll();

        $this->assertCount(2, $result);
    }
}
