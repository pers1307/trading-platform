<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DictionaryStockControllerTest extends WebTestCase
{
    public function testCanLoadIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/dictionary/stocks');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Акции');
        $this->assertSelectorExists('table');
    }
}
