<?php

namespace Svc\VideoBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Svc\VideoBundle\Entity\VideoGroup;
use Svc\VideoBundle\Exception\DefaultVideoGroupNotExistsException;
use Svc\VideoBundle\Repository\VideoGroupRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * helper function for video groups.
 */
class VideoGroupHelper
{
  public function __construct(
    private readonly bool $enableShortNames,
    private readonly VideoGroupRepository $videoGroupRep,
    private readonly EntityManagerInterface $entityManager,
    private readonly UrlGeneratorInterface $router)
  {
  }

  /**
   * create a row for the default video group if not exists.
   */
  public function initDefaultVideoGroup(): void
  {
    if ($this->videoGroupRep->findOneBy(['defaultGroup' => true])) {
      return;
    }

    $videoGroup = new VideoGroup();
    $videoGroup->setName('Allgemein');
    $videoGroup->setDefaultGroup(true);

    $this->entityManager->persist($videoGroup);
    $this->entityManager->flush();
  }

  /**
   * get the default video group or raise an exception.
   *
   * @throws Exception
   */
  public function getDefaultVideoGroup(): VideoGroup
  {
    $videoGroup = $this->videoGroupRep->findOneBy(['defaultGroup' => true]);
    if ($videoGroup) {
      return $videoGroup;
    }
    throw new DefaultVideoGroupNotExistsException();
  }

  /**
   * get all video groups.
   *
   * @param bool|null $onlyVisiblesOnHomePage if true, only videos with hideOnHomePage=false are returned
   */
  public function getVideoGroups(?bool $onlyVisiblesOnHomePage = false): ?array
  {
    if ($onlyVisiblesOnHomePage) {
      return $this->videoGroupRep->findAllExceptHidenOnHomePage();
    } else {
      return $this->videoGroupRep->findAll();
    }
  }

  /**
   * get a group for a specific id.
   */
  public function getVideoGroup(int $id): ?VideoGroup
  {
    return $this->videoGroupRep->find($id);
  }

  /**
   * get a video group by a shortname or (fallback) try by id.
   */
  public function getVideoGroupIDbyShortName(string $shortName): ?int
  {
    if ($this->enableShortNames) {
      $videoGroup = $this->videoGroupRep->findOneBy(['shortName' => $shortName]);
      if ($videoGroup) {
        return $videoGroup->getId();
      }
    }

    if (ctype_digit($shortName)) {
      return $this->videoGroupRep->find($shortName)->getId();
    }

    return null;
  }

  /**
   * generate the url for a video group, try to use the short form.
   */
  public function generateVideoGroupUrl(?VideoGroup $group, ?int $sort = 0): ?string
  {
    if (!$group) {
      return null;
    }
    $shortName = $group->getIDorShortname();

    try { // not sure, if trait is enabled...
      return $this->router->generate('svc_video_shortGroup', ['group' => $shortName, 'sort' => $sort ?? 0], UrlGeneratorInterface::ABSOLUTE_URL);
    } catch (Exception) {
      return $this->router->generate('svc_video_list', ['group' => $shortName, 'sort' => $sort ?? 0], UrlGeneratorInterface::ABSOLUTE_URL);
    }
  }
}
