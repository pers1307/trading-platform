<?php

namespace App\Repository;

use App\Entity\Deal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Deal>
 * @method Deal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deal::class);
    }

    public function findAllForList(): array
    {
        return $this->createQueryBuilder('d')
            ->addSelect('stock')
            ->addSelect('accaunt')
            ->leftJoin('d.stock', 'stock')
            ->innerJoin('d.accaunt', 'accaunt')
            ->orderBy('d.created', 'DESC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }
}
