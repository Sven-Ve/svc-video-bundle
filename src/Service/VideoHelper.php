<?php

namespace Svc\VideoBundle\Service;

use Exception;
use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Repository\VideoRepository;

/**
 * helper function for videos
 */
class VideoHelper
{


  private $videoRep;
  private $thumbnailDir;
  public function __construct(string $thumbnailDir, VideoRepository $videoRep)
  {
    $this->videoRep = $videoRep;
    $this->thumbnailDir = $thumbnailDir;
  }

  /**
   * get a list of all possible ratios for FormTypes
   * 
   * default: 1x1|4x3|16x9|21x9
   * could by overwritten via .env parameter VIDEO_RATIOS
   *
   * @return array|null
   */
  public static function getRatioList(): ?array
  {
    $ratioStr = $_ENV['VIDEO_RATIOS'] ?? '1x1|4x3|16x9|21x9';
    return explode('|', $ratioStr);
  }

  /**
   * try to get the thumbnail URL from streaming services
   *
   * @param Video $video
   * @return string|null the url to the thumbnail
   */
  public function getThumbnailUrl(Video $video): ?string
  {
    if ($video->getSourceType() == Video::SOURCE_VIMEO) {
      // see https://gist.github.com/anjan011/3b6d13a9f7a8642ecc4c
      try {
        $apiData = unserialize(file_get_contents("https://vimeo.com/api/v2/video/" . $video->getSourceID() . ".php"));
        if (is_array($apiData) and count($apiData) > 0) {
          return $apiData[0]['thumbnail_large'];
        }
      } catch (Exception $e) {
      }
    }
    return null;
  }

  public function copyThumbnail(Video $video): ?string
  {
    // https://www.cluemediator.com/how-to-save-an-image-from-a-url-in-php
    if ($video->getSourceType() == Video::SOURCE_VIMEO) {
//      try {
        $imgName = 'thumb_' . $video->getId() . '.webp';
        $imgPath = $this->thumbnailDir . '/' . $imgName;
        file_put_contents($imgPath, file_get_contents($video->getThumbnailUrl()));
        return $imgName;
//      } catch (Exception $e) {
//      }
    }
    return null;
  }

  public function getVideos(): ?array
  {
    return $this->videoRep->findAll();
  }
}
