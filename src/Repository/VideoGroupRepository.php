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

    public function findAllExceptHidenOnHomePage()
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.hideOnHomePage = false')
            ->orderBy('v.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }



}
