<?php

declare(strict_types=1);

namespace App\Service\Finam\Response;

class AuthDetailsResponse
{
    public function __construct(
        public string $accountId,
    ) {
    }
}
