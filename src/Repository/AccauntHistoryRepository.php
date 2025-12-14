<?php

namespace App\Repository;

use App\Entity\AccauntHistory;
use App\Entity\RiskProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RiskProfile>
 * @method RiskProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method RiskProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method RiskProfile[]    findAll()
 * @method RiskProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccauntHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccauntHistory::class);
    }

    public function findLatestByAccauntId(int $accauntId): ?AccauntHistory
    {
        return $this->createQueryBuilder('ah')
            ->where('ah.accaunt = :accauntId')
            ->setParameter('accauntId', $accauntId)
            ->orderBy('ah.created', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
