<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public const LOGIN_URL = '/login';
    public const OTHER_PAGE_URL = '/trades';

    public function testCanLoadLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', self::LOGIN_URL);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.lockscreen-credentials');
    }

    public function testCanNotLoadAnyOtherPage(): void
    {
        $client = static::createClient();
        $client->request('GET', self::OTHER_PAGE_URL);

        $this->assertResponseRedirects();
    }
}
