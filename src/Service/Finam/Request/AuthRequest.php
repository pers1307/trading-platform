<?php

declare(strict_types=1);

namespace App\Service\Finam\Request;

class AuthRequest
{
    public function __construct(
        public string $secret,
    ) {
    }
}
