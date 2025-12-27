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
        $url = getenv('CBR_KEYRATE_URL') ?: self::URL;
        $hostHeader = getenv('CBR_KEYRATE_HOST') ?: null;
        $userAgent = getenv('CBR_KEYRATE_USER_AGENT') ?: 'trading-platform/1.0';

        $options = [];
        $options['headers'] = [
            'User-Agent' => $userAgent,
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ];
        if ($hostHeader) {
            $options['headers']['Host'] = $hostHeader;
        }

        $response = $this->httpClient->request('GET', $url, $options);

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
