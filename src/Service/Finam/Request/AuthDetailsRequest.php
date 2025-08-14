<?php

declare(strict_types=1);

namespace App\Service\Finam\Request;

class AuthDetailsRequest
{
    public function __construct(
        public string $token,
    ) {
    }
}
