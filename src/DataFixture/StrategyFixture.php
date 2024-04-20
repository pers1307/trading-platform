<?php

namespace App\DataFixture;

use App\Entity\Strategy;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StrategyFixture extends Fixture
{
    public const MY_STRATEGY = 'MY_STRATEGY';
    public const MY_STRATEGY_TITLE = 'Моя стратегия';

    public const EMPTY_STRATEGY = 'EMPTY_STRATEGY';
    public const EMPTY_STRATEGY_TITLE = 'Стратегия не имеющая сделок';

    public static function getMyStrategy(): Strategy
    {
        return (new Strategy())
            ->setTitle(self::MY_STRATEGY_TITLE)
            ->setDescription('')
            ->setStatus(Strategy::STATUS_ACTIVE);
    }

    public static function getEmptyStrategy(): Strategy
    {
        return (new Strategy())
            ->setTitle(self::EMPTY_STRATEGY_TITLE)
            ->setDescription('')
            ->setStatus(Strategy::STATUS_ACTIVE);
    }

    public function load(ObjectManager $manager): void
    {
        $myStrategy = self::getMyStrategy();
        $manager->persist($myStrategy);

        $emptyStrategy = self::getEmptyStrategy();
        $manager->persist($emptyStrategy);

        $manager->flush();

        $this->addReference(self::MY_STRATEGY, $myStrategy);
        $this->addReference(self::EMPTY_STRATEGY, $emptyStrategy);
    }
}
