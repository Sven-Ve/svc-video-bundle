<?php

namespace Svc\VideoBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Svc\VideoBundle\Entity\Video;
use function Symfony\Component\String\u;

/**
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
  public const SORT_BY_DATE_DESC = 0;
  public const SORT_BY_TITLE = 1;
  public const SORT_BY_LIKES = 2;
  public const SORT_BY_VIEWS = 3;
  public const SORT_BY_DATE = 4;

  // t = text, f = field, d = direction
  public const SORT_FIELDS = [
    self::SORT_BY_DATE_DESC => ['t' => 'Date desc', 'f' => 'uploadDate', 'd' => 'desc'],
    self::SORT_BY_TITLE => ['t' => 'Title', 'f' => 'title', 'd' => 'asc'],
    self::SORT_BY_LIKES => ['t' => 'Likes', 'f' => 'likes', 'd' => 'desc'],
    self::SORT_BY_VIEWS => ['t' => 'Views', 'f' => 'calls', 'd' => 'desc'],
    self::SORT_BY_DATE => ['t' => 'Date', 'f' => 'uploadDate', 'd' => 'asc'],
  ];

  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Video::class);
  }

  public function cbAllVideos(): QueryBuilder
  {
    return $this->createQueryBuilder('v')
      ->orderBy('v.id', 'DESC');
  }

  public function videoStatsByGroup(): array
  {
    return $this->createQueryBuilder('v')
      ->select('vg.name, vg.description, sum(v.likes) as likes, sum(v.calls) as calls, count(v.id) as cnt')
      ->groupby('vg.name')
      ->join('v.videoGroup', 'vg')
      ->orderBy('vg.name', 'ASC')
      ->getQuery()
      ->getResult();
  }

  /**
   * @return Video[]
   */
  public function findBySearchQuery(string $query, int $limit=10): array {
    $searchTerms = $this->extractSearchTerms($query);

    if (0 === \count($searchTerms)) {
      return [];
    }

    $queryBuilder = $this->createQueryBuilder('v');

    foreach ($searchTerms as $key => $term) {
      $queryBuilder
        ->orWhere('v.title LIKE :v_'.$key)
        ->orWhere('v.description LIKE :v_'.$key)
        ->orWhere('v.subTitle LIKE :v_'.$key)
        ->setParameter('v_'.$key, '%'.$term.'%')
      ;
    }

    return $queryBuilder
      ->andWhere('v.hideOnHomePage=false')
      ->orderBy('v.id', 'DESC')
      ->setMaxResults($limit)
      ->getQuery()
      ->getResult();
  }

  private function extractSearchTerms(string $searchQuery): array
  {
    $searchQuery = u($searchQuery)->replaceMatches('/[[:space:]]+/', ' ')->trim();
    $terms = array_unique($searchQuery->split(' '));

    // ignore the search terms that are too short
    return array_filter($terms, static function ($term) {
      return 2 <= $term->length();
    });
  }
}
