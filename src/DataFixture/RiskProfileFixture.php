<?php

namespace App\DataFixture;

use App\Entity\RiskProfile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RiskProfileFixture extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            AccauntFixture::class,
            StrategyFixture::class,
            StockFixture::class,
        ];
    }

    public static function getRiskProfile(string $type): RiskProfile
    {
        $riskProfile = new RiskProfile();
        $riskProfile->setBalance(1000000);
        $riskProfile->setType($type);
        $riskProfile->setPersent(10);

        return $riskProfile;
    }

    public function load(ObjectManager $manager): void
    {
        $riskProfile = self::getRiskProfile(RiskProfile::TYPE_DEPOSIT);
        $riskProfile->setAccaunt($this->getReference(AccauntFixture::ACCAUNT_ONE));
        $riskProfile->setStrategy($this->getReference(StrategyFixture::MY_STRATEGY));
        $manager->persist($riskProfile);

        $riskProfile2 = self::getRiskProfile(RiskProfile::TYPE_TRADE);
        $riskProfile2->setAccaunt($this->getReference(AccauntFixture::ACCAUNT_ONE));
        $riskProfile2->setStrategy($this->getReference(StrategyFixture::MY_STRATEGY));
        $manager->persist($riskProfile2);

        $manager->flush();
    }
}
