<?php

namespace App\Service;

use App\Entity\RiskProfile;
use Doctrine\ORM\EntityManagerInterface;

class RiskProfileService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @return RiskProfile[]
     */
    public function findAll(): array
    {
        $riskProfileRepository = $this->entityManager->getRepository(RiskProfile::class);
        return $riskProfileRepository->findAll();
    }

    /**
     * @return RiskProfile[]
     * @todo покрыть тестами (?)
     */
    public function getIndexAll(): array
    {
        $riskProfiles = $this->findAll();

        $result = [];
        foreach ($riskProfiles as $riskProfile) {
            $key = "{$riskProfile->getAccaunt()->getId()}-{$riskProfile->getStrategy()->getId()}";
            $result[$key] = $riskProfile;
        }

        return $result;
    }
}
