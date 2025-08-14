<?php

declare(strict_types=1);

namespace App\Service\Finam\Resource;

use App\Service\Finam\HttpClient;

class AccountResource
{
    public const string RACE_PRICING_URL = '/v1/accounts/{accountId}';

    public function __construct(
        private readonly HttpClient $httpClient,
    ) {
    }

//    public function getAuthDetails(AuthDetailsRequest $authDetailsRequest): AuthDetailsResponse
//    {
//        $responseAsArray = $this->httpClient->request(
//            method: 'POST',
//            url: self::AUTH_DETAIL_URL,
//            options: [
//                'body' => json_encode([
//                    'token' => $authDetailsRequest->token
//                ])
//            ]
//        )->toArray();
//
//        return new AuthDetailsResponse($responseAsArray['account_ids'][0]);
//    }
}
