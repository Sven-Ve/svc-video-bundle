<?php

namespace Svc\VideoBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Svc\VideoBundle\Entity\_VideoSuperclass;
use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Repository\VideoRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * helper function for videos
 * get metadata, load thumbnail, get video objects, ...
 */
class VideoHelper
{
  private $videoRep;
  private $thumbnailDir;
  private $entityManager;
  private $requestStack;
  private $enablePrivate;
  private $enableShortNames;
  private $router;

  public function __construct(
    string $thumbnailDir,
    bool $enablePrivate,
    bool $enableShortNames,
    VideoRepository $videoRep,
    EntityManagerInterface $entityManager,
    RequestStack $requestStack,
    UrlGeneratorInterface $router
  ) {
    $this->videoRep = $videoRep;
    $this->enablePrivate = $enablePrivate;
    $this->enableShortNames = $enableShortNames;
    $this->thumbnailDir = $thumbnailDir;
    $this->entityManager = $entityManager;
    $this->requestStack = $requestStack;
    $this->router = $router;
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
      return $this->videoRep->findBy(['hideOnHomePage' => false]);
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
  private const ENC_CIPHER = "AES-128-CBC";
  private const SESS_ATTR_NAME = "svcv_password";


  /**
   * encrypt a password
   *
   * @param string $plainPassword
   * @return string
   */
  private function encryptVideoPassword(string $plainPassword): string
  {
    $ivlen = openssl_cipher_iv_length(self::ENC_CIPHER);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($plainPassword, self::ENC_CIPHER, $this->encKey, $options = OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, $this->encKey, $as_binary = true);
    $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);
    return $ciphertext;
  }

  /**
   * decryped a password
   *
   * @param string $encPassword
   * @return string|null
   */
  private function decryptPassword(string $encPassword): ?string
  {
    $c = base64_decode($encPassword);
    $ivlen = openssl_cipher_iv_length(self::ENC_CIPHER);
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len = 32);
    $ciphertext_raw = substr($c, $ivlen + $sha2len);
    $original_plaintext = openssl_decrypt($ciphertext_raw, self::ENC_CIPHER, $this->encKey, $options = OPENSSL_RAW_DATA, $iv);

    $calcmac = hash_hmac('sha256', $ciphertext_raw, $this->encKey, $as_binary = true);

    if (hash_equals($hmac, $calcmac)) // PHP 5.6+ Rechenzeitangriff-sicherer Vergleich
    {
      return $original_plaintext;
    }

    return null;
  }

  /**
   * check if password is correct
   *
   * @param string $plainPassword the plain text password or '' if session password should be used
   * @param string $encryptedPassword then encrypted password
   * @return boolean true if passwords match
   */
  public function checkPassword(string $plainPassword, string $encryptedPassword): bool
  {
    if (!$this->enablePrivate) {
      return true;
    }
    if (!$plainPassword) {
      $plainPassword = $this->requestStack->getSession()->get(self::SESS_ATTR_NAME, null);
    }

    $encrypedPassword = $this->decryptPassword($encryptedPassword);
    if (!$encryptedPassword) {
      return false;
    }

    if ($plainPassword === $encrypedPassword) {
      $this->requestStack->getSession()->set(self::SESS_ATTR_NAME, $plainPassword);
      return true;
    }
    return false;
  }


  /**
   * get encrypted password or null, if video / videogroup not private
   *
   * @param _VideoSuperclass $obj (Video or VideoGroup)
   * @return string|null
   */
  function getEncPassword(_VideoSuperclass $obj): ?string
  {
    if ($this->enablePrivate and $obj->getPlainPassword() and $obj->getIsPrivate()) {
      return $this->encryptVideoPassword($obj->getPlainPassword());
    } else {
      return null;
    }
  }

  /**
   * get decrypted password or null, if video / videogroup not private
   *
   * @param _VideoSuperclass $obj
   * @return string|null
   */
  function getDecrypedPassword(_VideoSuperclass $obj): ?string
  {
    if ($this->enablePrivate and $obj->getIsPrivate() and $obj->getPassword()) {
      return $this->decryptPassword($obj->getPassword());
    } else {
      return null;
    }
  }

  /**
   * generate a url for a video, using short forms if possible
   *
   * @param Video $video
   * @param string $currentRoute
   * @return string
   */
  function generateVideoUrl(Video $video, string $currentRoute): string
  {
    $url = $this->router->generate($currentRoute, ['id' => $video->getIDorShortname()], UrlGeneratorInterface::ABSOLUTE_URL);
    try { // not sure, if trait is enabled...
      if ($currentRoute == "svc_video_run") {
        if ($this->enableShortNames) {
          $url = $this->router->generate('svc_video_short_run1', ['id' => $video->getIDorShortname()], UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
          $url = $this->router->generate('svc_video_short_run', ['id' => $video->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        }
      } elseif ($currentRoute == 'svc_video_run_hn') {
        $url = $this->router->generate('svc_video_short_runHideNav', ['id' => $video->getIDorShortname()], UrlGeneratorInterface::ABSOLUTE_URL);
      }
    } catch (Exception $e) {
    }
    return $url;
  }
}
