<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * @return Notification[]
     */
    public function findLastFive(): array
    {
        return $this->createQueryBuilder('n')
            ->setMaxResults(5)
            ->orderBy('n.created', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function updateAllSetViewed(): void
    {
        $this->createQueryBuilder('n')
            ->update()
            ->set('n.viewed', 1)
            ->getQuery()
            ->execute();
    }
}
