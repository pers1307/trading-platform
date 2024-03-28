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
    const TQBR_URL = 'https://iss.moex.com/iss/engines/stock/markets/shares/boards/TQBR/securities.json';
    const TQPI_URL = 'https://iss.moex.com/iss/engines/stock/markets/shares/boards/TQPI/securities.json';

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
     * @todo: пробуем зарефакторить на функциональный метод
     * Получение данных - отдельный сервис
     * Обновление данных отдельный сервис
     * Где - то отдельно хранить константы
     * Сделать некий конвертер данных (это пойдет в юнит тесты)
     * Сделать некий обновлятор данных (это пойдет в интеграционные тесты)
     * Предусмотреть единичное обновление цены инструмента
     * стр. 172
     * Возвращает команду создания побочного эффекта, которую он хочет выполнить
     * Разделение бизнес логики и побочных эффектов
     *
     * @todo для будущего модуля рисков нужно ещё отслеживать максимальную, минимальную и прочие цены
     * а не только текущую, иначе детектить позиции будет сложно
     */
    public function update(): void
    {
        $response = $this->httpClient->request('GET', self::TQBR_URL);
        $data = $response->toArray();

        $stockRepository = $this->entityManager->getRepository(Stock::class);
        $stockRepository->upsertSecurity($data['securities']['data']);
        $stockRepository->updatePrices($data['marketdata']['data']);

        // Для TQPI
        $response = $this->httpClient->request('GET', self::TQPI_URL);
        $data = $response->toArray();

        $stockRepository = $this->entityManager->getRepository(Stock::class);
        $stockRepository->upsertSecurity($data['securities']['data']);
        $stockRepository->updatePrices($data['marketdata']['data']);
    }
}
