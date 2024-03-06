<?php

namespace App\DataFixture;

use App\Entity\Accaunt;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AccauntFixture extends Fixture
{
    public const ACCAUNT_ONE = 'ACCAUNT_ONE';
    public const ACCAUNT_TWO = 'ACCAUNT_TWO';

    public const ACCAUNT_ONE_TITLE = 'Счет №1';
    public const ACCAUNT_TWO_TITLE = 'Счет №2';

    public static function getOneAccaunt(): Accaunt
    {
        return (new Accaunt())
            ->setTitle(self::ACCAUNT_ONE_TITLE)
            ->setBrockerTitle('239900****CG');
    }

    public static function getTwoAccaunt(): Accaunt
    {
        return (new Accaunt())
            ->setTitle(self::ACCAUNT_TWO_TITLE)
            ->setBrockerTitle('239900****CG');
    }

    public function load(ObjectManager $manager): void
    {
        $accauntOne = self::getOneAccaunt();
        $manager->persist($accauntOne);

        $accauntTwo = self::getTwoAccaunt();
        $manager->persist($accauntTwo);

        $manager->flush();

        $this->addReference(self::ACCAUNT_ONE, $accauntOne);
        $this->addReference(self::ACCAUNT_TWO, $accauntTwo);
    }
}
