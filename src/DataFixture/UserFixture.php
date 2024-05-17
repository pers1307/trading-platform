<?php

namespace App\DataFixture;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public const TEST_USER = 'test';

    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public static function getUser(): User
    {
        return (new User())
            ->setName(self::TEST_USER)
            ->setRoles(["ROLE_USER"]);
    }

    public function getUserWithPassword(): User
    {
        $user = self::getUser();
        return $user
            ->setPassword($this->userPasswordHasher->hashPassword($user, "123"));
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->getUserWithPassword();
        $manager->persist($user);
        $manager->flush();

        $this->addReference(self::TEST_USER, $user);
    }
}
