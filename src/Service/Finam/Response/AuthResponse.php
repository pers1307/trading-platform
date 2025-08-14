<?php

declare(strict_types=1);

namespace App\Service\Finam\Response;

class AuthResponse
{
    public function __construct(
        public string $token,
    ) {
    }
}
