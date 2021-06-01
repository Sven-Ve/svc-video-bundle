<?php

namespace Svc\VideoBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Svc\VideoBundle\Entity\VideoGroup;
use Svc\VideoBundle\Repository\VideoGroupRepository;

/**
 * helper function for video groups
 */
class VideoGroupHelper
{

  private $videoGroupRep;
  private $entityManager;

  public function __construct(VideoGroupRepository $videoGroupRep, EntityManagerInterface $entityManager)
  {
    $this->videoGroupRep = $videoGroupRep;
    $this->entityManager = $entityManager;
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
   * @return array|null array of video groups
   */
  public function getVideoGroups(): ?array {
    return $this->videoGroupRep->findAll();
  }
}