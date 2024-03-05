<?php

namespace App\DataFixtures;

use App\Entity\Accaunt;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AccauntFixtures extends Fixture
{
    public const ACCAUNT_ONE = 'ACCAUNT_ONE';
    public const ACCAUNT_TWO = 'ACCAUNT_TWO';

    public const ACCAUNT_ONE_TITLE = 'Счет №1';
    public const ACCAUNT_TWO_TITLE = 'Счет №2';

    public function load(ObjectManager $manager): void
    {
        $accauntOne = new Accaunt();
        $accauntOne->setTitle(self::ACCAUNT_ONE_TITLE);
        $accauntOne->setBrockerTitle('239900****CG');
        $manager->persist($accauntOne);

        $accauntTwo = new Accaunt();
        $accauntTwo->setTitle(self::ACCAUNT_TWO_TITLE);
        $accauntTwo->setBrockerTitle('240000****CG');
        $manager->persist($accauntTwo);

        $manager->flush();

        $this->addReference(self::ACCAUNT_ONE, $accauntOne);
        $this->addReference(self::ACCAUNT_TWO, $accauntTwo);
    }
}
