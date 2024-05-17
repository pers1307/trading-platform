<?php

namespace App\Tests\Functional\Controller;

class AccauntHistoryControllerTest extends BaseControllerTest
{
    public const ACCAUNT_HISTORY_URL = '/accaunt/1/history';
    public const ACCAUNT_HISTORY_ADD_URL = '/accaunt/1/history/add';

    public function testCanLoadList(): void
    {
        $client = $this->getClientWithAuthUser();
        $client->request('GET', self::ACCAUNT_HISTORY_URL);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'История счета');
        $this->assertSelectorExists('table');
    }

    public function testCanLoadAdd(): void
    {
        $client = $this->getClientWithAuthUser();
        $client->request('GET', self::ACCAUNT_HISTORY_ADD_URL);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Добавить значение к счету');
        $this->assertSelectorExists('form');
    }
}
