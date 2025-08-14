<?php

declare(strict_types=1);

namespace App\Service\Finam;

use App\Service\Finam\Resource\AccountResource;
use App\Service\Finam\Resource\AuthResource;

/**
 * @todo нужно придумать механизм автоматической авторизации в апи,
 * чтобы не вызывать это дела каждый раз
 */
readonly class Client
{
    public function __construct(
        public AuthResource $authResource,
        public AccountResource $accountResource,
    ) {
    }
}
