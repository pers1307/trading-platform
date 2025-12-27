<?php

namespace App\Repository;

use App\Entity\AccauntInflation;
use DateTimeInterface;
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

    public function findLatestBeforeDate(int $accauntId, DateTimeInterface $date): ?AccauntInflation
    {
        return $this->createQueryBuilder('ai')
            ->andWhere('IDENTITY(ai.accaunt) = :accauntId')
            ->andWhere('ai.date < :date')
            ->setParameter('accauntId', $accauntId)
            ->setParameter('date', $date)
            ->orderBy('ai.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
