<?php

namespace App\Repository;

use App\Entity\RiskProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
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

    public function findByAccaunt(int $accauntId): array
    {
        return $this->createQueryBuilder('rp')
            ->addSelect('strategy')
            ->innerJoin('rp.strategy', 'strategy')
            ->where('IDENTITY(rp.accaunt) = :accauntId')
            ->orderBy('rp.id', 'ASC')
            ->setParameter('accauntId', $accauntId)
            ->getQuery()
            ->getResult();
    }

    public function findByAccauntAndStrategy(int $accauntId, int $strategyId): ?RiskProfile
    {
        return $this->createQueryBuilder('rp')
            ->where('IDENTITY(rp.accaunt) = :accauntId')
            ->andWhere('IDENTITY(rp.strategy) = :strategyId')
            ->orderBy('rp.id', 'ASC')
            ->setParameters(
                new ArrayCollection([
                    new Parameter('accauntId', $accauntId),
                    new Parameter('strategyId', $strategyId),
                ])
            )
            ->getQuery()
            ->getSingleResult();
    }
}
