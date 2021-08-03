<?php

namespace Svc\VideoBundle\Service;


use Svc\LogBundle\DataProvider\GeneralDataProvider;
use Svc\VideoBundle\Controller\VideoController;
use Svc\VideoBundle\Repository\VideoGroupRepository;
use Svc\VideoBundle\Repository\VideoRepository;

class LogDataProvider extends GeneralDataProvider
{

  private $videoSourceIDs = [];
  private $isVideoSourceIDsInitialized = false;
  private $vGroupSourceIDs = [];
  private $isVGroupSourceIDsInitialized = false;
  private $videoRep;
  private $videoGroupRep;

  public function __construct(VideoRepository $videoRep, VideoGroupRepository $videoGroupRep)
  {
    $this->videoRep = $videoRep;
    $this->videoGroupRep = $videoGroupRep;
  }

  /**
   * init the sourceType array
   *
   * @return boolean
   */
  protected function initSourceTypes(): bool
  {
    if ($this->isSourceTypesInitialized) {
      return true;
    }
    $this->sourceTypes[VideoController::OBJ_TYPE_VIDEO] = "video";
    $this->sourceTypes[VideoController::OBJ_TYPE_VGROUP] = "video group";
    $this->isSourceTypesInitialized = true;
    return true;
  }


  /**
   * get the text/description for a source ID / sourceType combination
   *
   * @param integer $sourceID
   * @param integer|null $sourceType
   * @return string
   */
  public function getSourceIDText(int $sourceID, ?int $sourceType = null): string
  {
    if ($sourceType === VideoController::OBJ_TYPE_VIDEO) {
      if (!$this->isVideoSourceIDsInitialized) {
        $this->initVideoSourceIDs();
      }
      return array_key_exists($sourceID, $this->videoSourceIDs) ? $this->videoSourceIDs[$sourceID] : $sourceID;
    } elseif ($sourceType === VideoController::OBJ_TYPE_VGROUP) {
      if (!$this->isVGroupSourceIDsInitialized) {
        $this->initVGroupSourceIDs();
      }
      return array_key_exists($sourceID, $this->vGroupSourceIDs) ? $this->vGroupSourceIDs[$sourceID] : $sourceID;
    }
    return strval($sourceID);
  }

  /**
   * read all video titles, store it in an array
   *
   * @return void
   */
  private function initVideoSourceIDs()
  {
    foreach ($this->videoRep->findAll() as $video) {
      $this->videoSourceIDs[$video->getId()] = $video->getTitle();
    }

    $this->isVideoSourceIDsInitialized = true;
  }

  /**
   * read all video group titles, store it in an array
   *
   * @return void
   */
  private function initVGroupSourceIDs()
  {
    foreach ($this->videoGroupRep->findAll() as $vGroup) {
      $this->vGroupSourceIDs[$vGroup->getId()] = $vGroup->getTitle();
    }
    $this->vGroupSourceIDs["0"] = "All videos";

    $this->isVGroupSourceIDsInitialized = true;
  }
}
