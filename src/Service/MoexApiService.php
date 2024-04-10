<?php

namespace App\Service;

use App\Constant\FinancialType;
use App\Converter\DataToMoexStockConverter;
use App\Dto\MoexStock;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MoexApiService
{
    const URL_ALL = 'https://iss.moex.com/iss/engines/stock/markets/shares/boards/%s/securities.json';
    const URL_ONE = 'https://iss.moex.com/iss/engines/stock/markets/shares/securities/%s.json';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly DataToMoexStockConverter $dataToMoexStockConverter,
    ) {
    }

    /**
     * @return MoexStock[]
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getMoexStocks(): array
    {
        $result = [];
        foreach (FinancialType::getAll() as $type) {
            $url = sprintf(self::URL_ALL, $type);
            $response = $this->httpClient->request('GET', $url);
            $data = $response->toArray();

            $moexStocks = $this->dataToMoexStockConverter->convert($data);
            $result = array_merge($result, $moexStocks);
        }

        return $result;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getMoexStockBySecId(string $secId): MoexStock
    {
        $url = sprintf(self::URL_ONE, $secId);
        $response = $this->httpClient->request('GET', $url);
        $data = $response->toArray();
        $moexStocks = $this->dataToMoexStockConverter->convert($data);

        $moexStock = array_values(
            array_filter(
                $moexStocks,
                static fn(MoexStock $moexStock) => in_array($moexStock->getBoardId(), FinancialType::getAll())
            )
        );

        return $moexStock[0];
    }
}
