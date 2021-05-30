<?php

namespace Svc\VideoBundle\Controller;

use DateTime;
use Svc\LikeBundle\Service\LikeHelper;
use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;



class VideoController extends AbstractController
{

  private $enableLikes;
  private $enableGroups;
  public function __construct(bool $enableLikes, bool $enableGroups)
  {
    $this->enableLikes = $enableLikes;
    $this->enableGroups = $enableGroups;
  }


  /**
   * show a video overview
   */
  public function show(VideoRepository $videoRep): Response
  {
    return $this->render('@SvcVideo/video/show.html.twig', [
      'videos' => $videoRep->findAll(),
      'enableLikes' => $this->enableLikes,
      'enableGroups' => $this->enableGroups
    ]);
  }

  /**
   * run a video
   *
   * @param Video $video
   * @param LikeHelper $likeHelper
   * @return Response
   */
  public function run(Video $video, LikeHelper $likeHelper): Response
  {
    $video->incCalls();
    $this->getDoctrine()->getManager()->flush();

    return $this->render('@SvcVideo/video/run.html.twig', [
      'video' => $video,
      'enableLikes' => $this->enableLikes,
      'liked' => $likeHelper->isLiked(LikeHelper::SOURCE_VIDEO, $video->getId()),
    ]);
  }

  /**
   * increase the like count
   *
   * @param Video $video
   * @param LikeHelper $likeHelper
   * @return Response
   */
  public function incLikes(Video $video, LikeHelper $likeHelper): Response
  {
    $response = new JsonResponse();
    $cookieName = null;

    if ($likeHelper->addLike(LikeHelper::SOURCE_VIDEO, $video->getId(), null, $cookieName)) {

      if ($cookieName) {
        $response->headers->setCookie(new Cookie($cookieName, 1, new DateTime('+1 week')));
      }

      $newValue = $video->incLikes();
      $this->getDoctrine()->getManager()->flush();

      $response->setData(['likes' => $newValue]);
    } else {
      $response->setStatusCode(422);
    }

    return $response;
  }
}
