<?php

namespace Svc\VideoBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Svc\VideoBundle\Entity\Tag;

class TagRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Tag::class);
  }

  public function getTagNamesAsJson(): string
  {
    $tags = $this->createQueryBuilder('t')
      ->orderBy('t.name', 'ASC')
      ->getQuery()
      ->getResult();

    return json_encode($tags);
  }

  /**
   * @param array $searchTerms
   * @return Tag[]
   */
  public function getTagsBySearchQuery(array $searchTerms): array {
    $tags = [];
    foreach ($searchTerms as $term) {
      $tag = $this->findOneBy(['name' => $term]);
      if ($tag) {
        $tags[] = $tag;
      }
    }
    return $tags;
  }
}
