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

    public function findAll(): array
    {
        $riskProfileRepository = $this->entityManager->getRepository(RiskProfile::class);
        return $riskProfileRepository->findAll();
    }
}
