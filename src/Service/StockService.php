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

    public function find(int $id): ?Stock
    {
        $stockRepository = $this->entityManager->getRepository(Stock::class);
        return $stockRepository->find($id);
    }

    /**
     * @return Stock[]
     */
    public function findAll(): array
    {
        $stockRepository = $this->entityManager->getRepository(Stock::class);
        return $stockRepository->findAll();
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

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @todo покрыть интеграцилнным тестом
     */
    public function updateByStockId(int $stockId): void
    {
        $stockRepository = $this->entityManager->getRepository(Stock::class);
        $stock = $stockRepository->find($stockId);

        $moexStock = $this->moexApiService->getMoexStockBySecId($stock->getSecId());
        $stockRepository->upsertByMoexStock($moexStock);
    }
}
