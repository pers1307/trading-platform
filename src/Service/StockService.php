<?php

namespace App\Service;

use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class StockService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MoexApiService $moexApiService,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function update(): void
    {
        $moexStocks = $this->moexApiService->getMoexStocks();
        $stockRepository = $this->entityManager->getRepository(Stock::class);

        foreach ($moexStocks as $moexStock) {
            $stockRepository->upsertByMoexStock($moexStock);
        }
    }
}
