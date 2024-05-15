<?php

namespace App\Tests\Functional\Controller\Trade;

use App\Tests\Functional\Controller\BaseControllerTest;

class RemoveTradeControllerTest extends BaseControllerTest
{
    public const CONFIRM_REMOVE_URL = '/trades/1/confirm/remove';
    public const CONFIRM_REMOVE_NOT_FOUND_URL = '/trades/1000/confirm/remove';
    public const REMOVE_NOT_FOUND_URL = '/trades/1000/remove';

    public function testCanLoadConfirmRemove(): void
    {
        $client = $this->getClientWithAuthUser();
        $client->request('GET', self::CONFIRM_REMOVE_URL);

        $this->assertResponseIsSuccessful();
    }

    public function testNotFoundConfirmRemove(): void
    {
        $client = $this->getClientWithAuthUser();
        $client->request('GET', self::CONFIRM_REMOVE_NOT_FOUND_URL);

        $this->assertResponseStatusCodeSame(404, 'Трейда не существует');
    }

    public function testNotFoundTradeForRemoveRemove(): void
    {
        $client = $this->getClientWithAuthUser();
        $client->request('POST', self::REMOVE_NOT_FOUND_URL);

        $this->assertResponseStatusCodeSame(404, 'Трейда не существует');
    }
}
