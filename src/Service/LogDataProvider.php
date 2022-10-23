<?php

namespace Svc\VideoBundle\Service;

use Svc\LogBundle\DataProvider\GeneralDataProvider;
use Svc\VideoBundle\Enum\ObjectType;
use Svc\VideoBundle\Repository\VideoGroupRepository;
use Svc\VideoBundle\Repository\VideoRepository;

class LogDataProvider extends GeneralDataProvider
{
  private array $videoSourceIDs = [];

  private bool $isVideoSourceIDsInitialized = false;

  private array $vGroupSourceIDs = [];

  private bool $isVGroupSourceIDsInitialized = false;

  public function __construct(private readonly VideoRepository $videoRep, private readonly VideoGroupRepository $videoGroupRep)
  {
  }

  /**
   * init the sourceType array.
   */
  protected function initSourceTypes(): bool
  {
    if ($this->isSourceTypesInitialized) {
      return true;
    }
    $this->sourceTypes[ObjectType::VIDEO->value] = ObjectType::VIDEO->label();
    $this->sourceTypes[ObjectType::VGROUP->value] = ObjectType::VGROUP->label();
    $this->isSourceTypesInitialized = true;

    return true;
  }

  /**
   * get the text/description for a source ID / sourceType combination.
   */
  public function getSourceIDText(int $sourceID, ?int $sourceType = null): string
  {
    if ($sourceType === ObjectType::VIDEO->value) {
      if (!$this->isVideoSourceIDsInitialized) {
        $this->initVideoSourceIDs();
      }

      return array_key_exists($sourceID, $this->videoSourceIDs) ? $this->videoSourceIDs[$sourceID] : $sourceID;
    } elseif ($sourceType === ObjectType::VGROUP->value) {
      if (!$this->isVGroupSourceIDsInitialized) {
        $this->initVGroupSourceIDs();
      }

      return array_key_exists($sourceID, $this->vGroupSourceIDs) ? $this->vGroupSourceIDs[$sourceID] : $sourceID;
    }

    return strval($sourceID);
  }

  /**
   * read all video titles, store it in an array.
   */
  private function initVideoSourceIDs(): void
  {
    foreach ($this->videoRep->findAll() as $video) {
      $this->videoSourceIDs[$video->getId()] = $video->getTitle();
    }

    $this->isVideoSourceIDsInitialized = true;
  }

  /**
   * read all video group titles, store it in an array.
   */
  private function initVGroupSourceIDs(): void
  {
    foreach ($this->videoGroupRep->findAll() as $vGroup) {
      $this->vGroupSourceIDs[$vGroup->getId()] = $vGroup->getTitle();
    }
    $this->vGroupSourceIDs['0'] = 'All videos';

    $this->isVGroupSourceIDsInitialized = true;
  }
}
