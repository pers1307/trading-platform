<?php

namespace App\Tests\Integration;

use App\Constant\FinancialType;
use App\DataFixture\MoexApiFixture;
use App\Repository\StockRepository;
use App\Service\MoexApiService;
use App\Service\StockService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class StockServiceTest extends KernelTestCase
{
    public function testUpdate(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $mockResponseMock = $this->getMockBuilder(MockResponse::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockResponseMock
            ->method('toArray')
            ->willReturn(MoexApiFixture::getAll());

        $httpClientStub = $this->getMockBuilder(HttpClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $httpClientStub
            ->method('request')
            ->with(
                'GET',
                $this->logicalOr(
                    $this->equalTo(sprintf(MoexApiService::URL_ALL, FinancialType::TQBR)),
                    $this->equalTo(sprintf(MoexApiService::URL_ALL, FinancialType::TQPI))
                )
            )
            ->willReturn($mockResponseMock);

        $container->set(HttpClientInterface::class, $httpClientStub);

        // Запуск
        $stockService = $container->get(StockService::class);
        $stockService->update();

        // Проверка данных
        $stockRepository = $container->get(StockRepository::class);
        $ozonStock = $stockRepository->findOneBy(['secId' => 'OZON']);
        $ozonRowData = MoexApiFixture::getOZONData();

        $this->assertNotEmpty($ozonStock);
        $this->assertEquals($ozonRowData['securities']['data'][0][0], $ozonStock->getSecId());
        $this->assertEquals($ozonRowData['securities']['data'][0][9], $ozonStock->getTitle());
        $this->assertEquals($ozonRowData['marketdata']['data'][0][10], $ozonStock->getLow());
        $this->assertEquals($ozonRowData['marketdata']['data'][0][11], $ozonStock->getHigh());

        $sberStock = $stockRepository->findOneBy(['secId' => 'SBER']);
        $sberRowData = MoexApiFixture::getSBERData();

        $this->assertNotEmpty($sberStock);
        $this->assertEquals($sberRowData['securities']['data'][0][0], $sberStock->getSecId());
        $this->assertEquals($sberRowData['securities']['data'][0][9], $sberStock->getTitle());
        $this->assertEquals($sberRowData['marketdata']['data'][0][10], $sberStock->getLow());
        $this->assertEquals($sberRowData['marketdata']['data'][0][11], $sberStock->getHigh());
    }
}
