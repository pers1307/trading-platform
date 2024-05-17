<?php

namespace App\Tests\Functional\Controller;

class RiskProfileControllerTest extends BaseControllerTest
{
    public function testCanLoadIndex(): void
    {
        $client = $this->getClientWithAuthUser();
        $client->request('GET', '/risk-profiles');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Риск профили');
        $this->assertSelectorExists('table');
    }
}
