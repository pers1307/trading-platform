<?php

namespace App\Repository;

use App\Entity\RiskProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RiskProfile>
 * @method RiskProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method RiskProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method RiskProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RiskProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RiskProfile::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('rp')
            ->addSelect('strategy')
            ->addSelect('accaunt')
            ->innerJoin('rp.strategy', 'strategy')
            ->innerJoin('rp.accaunt', 'accaunt')
            ->orderBy('rp.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
