<?php

namespace Svc\VideoBundle\Repository;

use Svc\VideoBundle\Entity\VideoGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VideoGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method VideoGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method VideoGroup[]    findAll()
 * @method VideoGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoGroup::class);
    }

    // /**
    //  * @return VideoGroup[] Returns an array of VideoGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VideoGroup
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
