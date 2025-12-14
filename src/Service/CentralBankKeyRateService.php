<?php

namespace App\Service;

use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CentralBankKeyRateService
{
    public const string URL = 'https://www.cbr.ru/hd_base/keyrate/';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    /**
     * Возвращает последнее значение ключевой ставки ЦБ с официального сайта.
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getLatestKeyRate(): float
    {
        $response = $this->httpClient->request('GET', self::URL);

        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('Не удалось получить ключевую ставку ЦБ: HTTP ' . $response->getStatusCode());
        }

        $content = $response->getContent();
        $crawler = new Crawler($content);
        $rows = $crawler->filter('table.data tr');

        // Первая строка — заголовок, дальше идут данные в порядке убывания дат.
        if ($rows->count() < 2) {
            throw new RuntimeException('Не удалось найти таблицу ставок ЦБ на странице.');
        }

        $cells = $rows->eq(1)->filter('td');
        if ($cells->count() < 2) {
            throw new RuntimeException('Не удалось распарсить значение ставки ЦБ.');
        }

        $rawRate = trim($cells->eq(1)->text());
        $normalizedRate = str_replace([',', ' ', "\u{00A0}"], ['.', '', ''], $rawRate);

        if (!is_numeric($normalizedRate)) {
            throw new RuntimeException('Некорректный формат ставки ЦБ: ' . $rawRate);
        }

        return (float) $normalizedRate;
    }
}
