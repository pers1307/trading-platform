<?php

declare(strict_types=1);

namespace App\Service\Finam\Resource;

use App\Service\Finam\HttpClient;
use App\Service\Finam\Request\AuthDetailsRequest;
use App\Service\Finam\Request\AuthRequest;
use App\Service\Finam\Response\AuthDetailsResponse;
use App\Service\Finam\Response\AuthResponse;

class AuthResource
{
    private const string AUTH_URL = '/v1/sessions';

    private const string AUTH_DETAIL_URL = '/v1/sessions/details';

    public function __construct(
        private readonly HttpClient $httpClient,
    ) {
    }

    public function getAuthToken(AuthRequest $authRequest): AuthResponse
    {
        $responseAsArray = $this->httpClient->request(
            method: 'POST',
            url: self::AUTH_URL,
            options: [
                'body' => json_encode([
                    'secret' => $authRequest->secret
                ])
            ]
        )->toArray();

        return new AuthResponse($responseAsArray['token']);

        /**
         * @todo пример того, как должно быть
         */
//        return new RacePricingResponse(
//            $this->client->send(
//                'POST',
//                static::RACE_PRICING_URL,
//                [
//                    'OriginCode' => $originCode,
//                    'DestinationCode' => $destinationCode,
//                    'DepartureDate' => $departureDate,
//                ],
//                self::LOG_RESOURCE
//            )->getData()
//        );
    }

    public function getAuthDetails(AuthDetailsRequest $authDetailsRequest): AuthDetailsResponse
    {
        $responseAsArray = $this->httpClient->request(
            method: 'POST',
            url: self::AUTH_DETAIL_URL,
            options: [
                'body' => json_encode([
                    'token' => $authDetailsRequest->token
                ])
            ]
        )->toArray();

        return new AuthDetailsResponse($responseAsArray['account_ids'][0]);
    }
}
