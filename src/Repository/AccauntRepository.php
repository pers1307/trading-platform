<?php

namespace App\Repository;

use App\Entity\Accaunt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Accaunt>
 *
 * @method Accaunt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Accaunt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Accaunt[]    findAll()
 * @method Accaunt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccauntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Accaunt::class);
    }

//    /**
//     * @return VinylMix[] Returns an array of VinylMix objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?VinylMix
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
