<?php

namespace App\Tests\Functional\Controller;

class AccauntControllerTest extends BaseControllerTest
{
    public function testCanLoadIndex(): void
    {
        $client = $this->getClientWithAuthUser();
        $client->request('GET', '/accaunts');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Счета');
        $this->assertSelectorExists('table');
    }
}
