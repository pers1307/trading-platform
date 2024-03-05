<?php

namespace App\DataFixtures;

use App\Entity\Strategy;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StrategyFixtures extends Fixture
{
    public const MY_STRATEGY = 'MY_STRATEGY';
    public const MY_STRATEGY_TITLE = 'Моя стратегия';

    public function load(ObjectManager $manager): void
    {
        $strategy = new Strategy();

        $strategy->setTitle(self::MY_STRATEGY_TITLE);
        $strategy->setDescription('');
        $strategy->setStatus('active');
        $manager->persist($strategy);

        $manager->flush();

        $this->addReference(self::MY_STRATEGY, $strategy);
    }
}
