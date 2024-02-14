<?php

namespace App\Service;

use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class StockService
{
    const URL = 'https://iss.moex.com/iss/engines/stock/markets/shares/boards/TQBR/securities.json';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly HttpClientInterface $httpClient,
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
        $response = $this->httpClient->request('GET', self::URL);
        $data = $response->toArray();

        $stockRepository = $this->entityManager->getRepository(Stock::class);
        $stockRepository->upsertSecurity($data['securities']['data']);
        $stockRepository->updatePrices($data['marketdata']['data']);
    }
}
