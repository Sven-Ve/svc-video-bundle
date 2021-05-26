<?php

namespace Svc\VideoBundle\Service;

use Svc\VideoBundle\Repository\VideoRepository;

/**
 * helper function for videos
 */
class VideoHelper {


  protected $videoRep;
  public function __construct(VideoRepository $videoRep)  {
    
    $this->videoRep = $videoRep;
  }

  /**
   * get a list of all possible ratios for FormTypes
   * 
   * default: video|link
   * could by overwritten via .env parameter VIDEO_RATIOS
   *
   * @return array|null
   */
  public static function getRatioList(): ?array {
    $ratioStr = $_ENV['VIDEO_RATIOS'] ?? '1x1|4x3|16x9|21x9';
    return explode('|', $ratioStr);
  }

  public function getVideos(): ?array {
    return $this->videoRep->findAll();
  }
}
