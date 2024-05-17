<?php

namespace App\Tests\Functional\Controller;

class DictionaryStockControllerTest extends BaseControllerTest
{
    public function testCanLoadIndex(): void
    {
        $client = $this->getClientWithAuthUser();
        $client->request('GET', '/dictionary/stocks');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Акции');
        $this->assertSelectorExists('table');
    }
}
