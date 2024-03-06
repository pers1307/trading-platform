<?php

namespace App\DataFixture;

use App\Entity\Strategy;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StrategyFixture extends Fixture
{
    public const MY_STRATEGY = 'MY_STRATEGY';
    public const MY_STRATEGY_TITLE = 'Моя стратегия';

    public static function getMyStrategy(): Strategy
    {
        return (new Strategy())
            ->setTitle(self::MY_STRATEGY_TITLE)
            ->setDescription('')
            ->setStatus(Strategy::STATUS_ACTIVE);
    }

    public function load(ObjectManager $manager): void
    {
        $strategy = self::getMyStrategy();
        $manager->persist($strategy);
        $manager->flush();

        $this->addReference(self::MY_STRATEGY, $strategy);
    }
}
