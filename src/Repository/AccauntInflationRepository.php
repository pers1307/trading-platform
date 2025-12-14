<?php

namespace App\Repository;

use App\Entity\AccauntInflation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccauntInflation>
 * @method AccauntInflation|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccauntInflation|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccauntInflation[]    findAll()
 * @method AccauntInflation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccauntInflationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccauntInflation::class);
    }
}
