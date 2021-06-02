<?php

namespace Svc\VideoBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
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
  private $entityManager;
  public function __construct(string $thumbnailDir, VideoRepository $videoRep, EntityManagerInterface $entityManager)
  {
    $this->videoRep = $videoRep;
    $this->thumbnailDir = $thumbnailDir;
    $this->entityManager = $entityManager;
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

  /**
   * load missing thumbnail URLs or all URLs, if $force = true
   *
   * @param boolean|null $force
   * @param string|null $msg
   * @return boolean
   */
  public function getMissingThumbnailUrl(?bool $force = false, ?string &$msg = null): bool
  {
    $videos = $force ? $this->videoRep->findAll() : $this->videoRep->findBy(['thumbnailUrl' => null]);

    foreach ($videos as $video) {
      $msg .= $video->getTitle() . ": ";
      $url = $this->getThumbnailUrl($video);
      if ($url) {
        $msg .= "loaded.\n";
        $video->setThumbnailUrl($url);
      } else {
        $msg .= "no thumbnail url found.\n";
      }
    }

    $this->entityManager->flush();
    return true;
  }

  public function copyThumbnail(Video $video): ?string
  {
    // https://www.cluemediator.com/how-to-save-an-image-from-a-url-in-php
    if ($video->getSourceType() == Video::SOURCE_VIMEO) {
      try {
        $imgName = 'thumb_' . $video->getId() . '.webp';
        $imgPath = $this->thumbnailDir . '/' . $imgName;
        file_put_contents($imgPath, file_get_contents($video->getThumbnailUrl()));
        return $imgName;
      } catch (Exception $e) {
      }
    }
    return null;
  }

  /**
   * get videos for a group or all videos, if group = null
   *
   * @param integer|null $group
   * @return array|null
   */
  public function getVideoByGroup(?int $group): ?array
  {
    if ($group) {
      return $this->videoRep->findBy(['videoGroup' => $group]);
    } else {
      return $this->videoRep->findAll();
    }
  }

  /**
   * create the thumbnail directory
   *
   * @param string|null $errMsg by reference: give the error message back
   * @return boolean true = successfull
   */
  public function createThumbnailDir(?string &$msg): bool
  {
    if (file_exists($this->thumbnailDir)) {
      if (is_dir($this->thumbnailDir)) {
        $msg = "Directory $this->thumbnailDir exists.";
        return true;
      } else {
        $msg = "Directory $this->thumbnailDir exists, but is a file";
        return false;
      }
    }
    try {
      mkdir($this->thumbnailDir);
    } catch (Exception $e) {
      $msg = "Cannot create ThumbnailDir: " . $e->getMessage();
      return false;
    }
    $msg = "Directory $this->thumbnailDir created.";
    return true;
  }
}
