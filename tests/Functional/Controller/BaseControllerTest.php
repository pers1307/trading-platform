<?php

namespace App\Tests\Functional\Controller;

use App\DataFixture\UserFixture;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseControllerTest extends WebTestCase
{
    protected function getClientWithAuthUser(): KernelBrowser
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByName(UserFixture::TEST_USER);
        return $client->loginUser($testUser);
    }
}
