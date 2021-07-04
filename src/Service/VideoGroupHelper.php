<?php

namespace Svc\VideoBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Svc\VideoBundle\Entity\VideoGroup;
use Svc\VideoBundle\Repository\VideoGroupRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * helper function for video groups
 */
class VideoGroupHelper
{

  private $videoGroupRep;
  private $entityManager;
  private $enableShortNames;
  private $router;

  public function __construct(bool $enableShortNames, VideoGroupRepository $videoGroupRep, EntityManagerInterface $entityManager, UrlGeneratorInterface $router)
  {
    $this->enableShortNames = $enableShortNames;
    $this->videoGroupRep = $videoGroupRep;
    $this->entityManager = $entityManager;
    $this->router = $router;
  }

  /**
   * create a row for the default video group if not exists
   *
   * @return void
   */
  public function initDefaultVideoGroup()
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
   * get the default video group or raise an exception
   *
   * @return Target
   */
  public function getDefaultVideoGroup(): VideoGroup
  {
    $videoGroup = $this->videoGroupRep->findOneBy(['defaultGroup' => true]);
    if ($videoGroup) {
      return $videoGroup;
    }
    throw new Exception('Default video group not found. Please inialize the app.');
  }

  /**
   * get all video groups
   *
   * @param boolean|null $onlyVisiblesOnHomePage if true, only videos with hideOnHomePage=false are returned
   * @return array|null
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
   * get a group for a specific id
   *
   * @param integer $id
   * @return VideoGroup|null
   */
  public function getVideoGroup(int $id): ?VideoGroup
  {
    return $this->videoGroupRep->find($id);
  }


  /**
   * get a video group by a shortname or (fallback) try by id
   *
   * @param string $shortName
   * @return integer|null
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
   * generate the url for a video group, try to use the short form
   *
   * @param VideoGroup|null $group
   * @return string
   */
  public function generateVideoGroupUrl(?VideoGroup $group): string
  {
    if (!$group) {
      return false;
    }
    $shortName = $group->getIDorShortname();

    try { // not sure, if trait is enabled...
      return $this->router->generate('svc_video_shortGroup', ['group' => $shortName], UrlGeneratorInterface::ABSOLUTE_URL);
    } catch (Exception $e) {
      return $this->router->generate('svc_video_list', ['group' => $shortName], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    return $this->router->generate('svc_video_list');
  }
}
