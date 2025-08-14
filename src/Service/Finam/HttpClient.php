<?php

declare(strict_types=1);

namespace App\Service\Finam;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

readonly class HttpClient
{
    public const string ENDPOINT = 'https://api.finam.ru';

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->httpClient->request(
            $method,
            self::ENDPOINT . $url,
            $options
        );
    }
}
