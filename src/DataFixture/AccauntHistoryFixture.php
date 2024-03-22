<?php

namespace App\DataFixture;

use App\Entity\AccauntHistory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AccauntHistoryFixture extends Fixture
{
    public function getDependencies(): array
    {
        return [
            AccauntFixture::class,
        ];
    }

    public const ACCAUNT_ONE = 'ACCAUNT_ONE';

    /**
     * @return AccauntHistory[]
     */
    public static function getHistory(): array
    {
        return [
            (new AccauntHistory())
                ->setBalance(100),
            (new AccauntHistory())
                ->setBalance(200),
            (new AccauntHistory())
                ->setBalance(300),
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $history = self::getHistory();
        foreach ($history as $accauntHistory) {
            $accauntHistory->setAccaunt($this->getReference(AccauntFixture::ACCAUNT_ONE));
            $manager->persist($accauntHistory);
        }

        $manager->flush();
    }
}
