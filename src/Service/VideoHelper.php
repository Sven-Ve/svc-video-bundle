<?php

namespace Svc\VideoBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Repository\VideoRepository;

/**
 * helper function for videos
 * get metadata, load thumbnail, get video objects, ...
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
   * load metadata from video streaming services
   *
   * @param Video $video by reference
   * @return boolean true = success
   */
  public function getVideoMetadata(Video &$video): bool
  {
    if ($video->getSourceType() == Video::SOURCE_VIMEO) {
      // see https://gist.github.com/anjan011/3b6d13a9f7a8642ecc4c
      try {
        $apiData = unserialize(file_get_contents("https://vimeo.com/api/v2/video/" . $video->getSourceID() . ".php"));

        if (is_array($apiData) and count($apiData) > 0) {
          $uploadDate = date_create_from_format('Y-m-d G:i:s', $apiData[0]['upload_date']);
          $video->setUploadDate($uploadDate);
          $video->setThumbnailUrl($apiData[0]['thumbnail_large']);
          return true;
        }
      } catch (Exception $e) {
        return false;
      }
    } elseif ($video->getSourceType() == Video::SOURCE_YOUTUBE) {
      $video->setThumbnailUrl("https://img.youtube.com/vi/" . $video->getSourceID() . "/mqdefault.jpg");
      return true;
    }
    return false;
  }

  /**
   * load missing metadata (thumbnail, date) or all metadata, if $force = true
   *
   * @param boolean|null $force
   * @param string|null $msg
   * @return boolean
   */
  public function getMissingMetadata(?bool $force = false, ?string &$msg = null): bool
  {
    $videos = $force ? $this->videoRep->findAll() : $this->videoRep->findBy(['thumbnailUrl' => null]);

    foreach ($videos as $video) {
      $msg .= $video->getTitle() . ": ";
      if ($this->getVideoMetadata($video)) {
        $msg .= "loaded.\n";
      } else {
        $msg .= "no thumbnail url found.\n";
      }
    }

    $this->entityManager->flush();
    return true;
  }

  /**
   * load missing thumbnails to local server or all thumbnails, if $force = true
   *
   * @param boolean|null $force
   * @param string|null $msg
   * @return boolean
   */
  public function getMissingThumbnails(?bool $force = false, ?string &$msg = null): bool
  {
    $videos = $force ? $this->videoRep->findAll() : $this->videoRep->findBy(['thumbnailPath' => null]);

    foreach ($videos as $video) {
      $msg .= $video->getTitle() . ": ";
      $path = $this->copyThumbnail($video, $force);
      if ($path) {
        $video->setThumbnailPath($path);
        $msg .= "copied.\n";
      } else {
        $msg .= "no thumbnail created.\n";
      }
    }

    $this->entityManager->flush();
    return true;
  }

  /**
   * copy thumbnail from streaming service to our local server
   *
   * @param Video $video
   * @param boolean|null $force
   * @return string|null
   */
  public function copyThumbnail(Video $video, ?bool $force = false): ?string
  {
    if ($force and $video->getThumbnailPath()) {
      try {
        unlink($this->thumbnailDir . '/' . $video->getThumbnailPath());
      } catch (Exception $e) {
      }
    }

    if ($video->getSourceType() == Video::SOURCE_VIMEO) {
      try {
        $imgName = 'thumb_' . $video->getId() . '-' . uniqid() . '.webp';
        $imgPath = $this->thumbnailDir . '/' . $imgName;
        file_put_contents($imgPath, file_get_contents($video->getThumbnailUrl()));
        return $imgName;
      } catch (Exception $e) {
      }
    } elseif ($video->getSourceType() == Video::SOURCE_YOUTUBE) {
      try {
        $imgName = 'thumb_' . $video->getId() . '-' . uniqid() . '.jpg';
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


  private $encKey = "a213123jsakdnjasdhquwhequez2eh328z4982zehqwkjdnaksjdniuhd";
  private $encCipher = "AES-128-CBC";
  /**
   * hash the password for a video
   *
   * @param integer $id videoId
   * @param string $plainPassword
   * @return string
   */
  public function encryptVideoPassword(string $plainPassword): string
  {
    $ivlen = openssl_cipher_iv_length($this->encCipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($plainPassword, $this->encCipher, $this->encKey, $options = OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, $this->encKey, $as_binary = true);
    $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);
    return $ciphertext;
  }

  public function decryptVideoPassword(string $encPassword): string
  {
    $c = base64_decode($encPassword);
    $ivlen = openssl_cipher_iv_length($this->encCipher);
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len = 32);
    $ciphertext_raw = substr($c, $ivlen + $sha2len);
    $original_plaintext = openssl_decrypt($ciphertext_raw, $this->encCipher, $this->encKey, $options = OPENSSL_RAW_DATA, $iv);
    return $original_plaintext;

    $calcmac = hash_hmac('sha256', $ciphertext_raw, $this->encKey, $as_binary = true);

    if (hash_equals($hmac, $calcmac)) // PHP 5.6+ Rechenzeitangriff-sicherer Vergleich
    {
      echo $original_plaintext . "\n";
    }
  }
}
