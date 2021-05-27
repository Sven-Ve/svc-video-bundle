<?php

namespace Svc\VideoBundle\Controller;

use App\Service\LikeHelper;
use DateTime;
use Svc\VideoBundle\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/video/{_locale}", requirements={"_locale": "%app.supported_locales%"})
 */

class VideoRunController extends AbstractController
{

  /**
   * @Route("/{id}", name="video_run", methods={"GET"})
   */
  public function run(Video $video /*, LikeHelper $likeHelper*/): Response
  {

    $video->incCalls();
    $this->getDoctrine()->getManager()->flush();

    return $this->render('@SvcVideo/video/run.html.twig', [
      'video' => $video,
//      'liked' => $likeHelper->isLiked(LikeHelper::SOURCE_VIDEO, $video->getId()),
    ]);
  }

  /**
   * @Route("/inc-likes/{id}", name="video_inc_likes", methods={"GET"})
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
