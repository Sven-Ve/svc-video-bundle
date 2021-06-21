<?php

namespace Svc\VideoBundle\Controller;

use DateTime;
use Svc\LikeBundle\Service\LikeHelper;
use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Form\EnterPasswordType;
use Svc\VideoBundle\Repository\VideoGroupRepository;
use Svc\VideoBundle\Repository\VideoRepository;
use Svc\VideoBundle\Service\VideoGroupHelper;
use Svc\VideoBundle\Service\VideoHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class VideoController extends AbstractController
{

  private const OBJ_TYPE_VIDEO = 1;
  private const OBJ_TYPE_VGROUP = 2;
  private $enableLikes;
  private $enableGroups;
  private $homeRoute;
  public function __construct(bool $enableLikes, bool $enableGroups, string $homeRoute)
  {
    $this->enableLikes = $enableLikes;
    $this->enableGroups = $enableGroups;
    $this->homeRoute = $homeRoute;
  }

  /**
   * list videos 
   */
  public function list(?int $id = null, ?bool $hideNav = false, ?bool $hideGroups = false, VideoHelper $videoHelper, VideoGroupHelper $videoGroupHelper, Request $request): Response
  {
    $groups = null;
    $currentGroup = null;

    if ($this->enableGroups) {
      $groups = $videoGroupHelper->getVideoGroups(true);
      if ($id) {
        $currentGroup = $videoGroupHelper->getVideoGroup($id);
        if (!$currentGroup) {
          return $this->redirectToRoute('svc_video_list');
        }
        $hideGroups = $hideGroups ? true : $currentGroup->getHideGroups();
        $hideNav = $hideNav ? true : $currentGroup->getHideNav();

        if ($currentGroup->getIsPrivate()) {
          if (!$videoHelper->checkPassword('', $currentGroup->getPassword())) {
            return $this->redirectToRoute('svc_video_pwd', ['id' => $currentGroup->getId(), 'ot' => self::OBJ_TYPE_VGROUP, 'path' => $request->attributes->get('_route')]);
          }
        }
      }

    }

    return $this->render('@SvcVideo/video/list.html.twig', [
      'videos' => $videoHelper->getVideoByGroup($id),
      'enableLikes' => $this->enableLikes,
      'groups' => $groups,
      'currentGroup' => $currentGroup,
      'hideGroups' => $hideGroups,
      'hideNav' => $hideNav,
    ]);
  }

  /**
   * run a video
   *
   * @param string $id numeric id or shortName
   * @return Response
   */
  public function run(string $id, ?bool $hideNav = false, LikeHelper $likeHelper, VideoRepository $videoRep, Request $request, VideoHelper $videoHelper): Response
  {
    $video = null;
    if (ctype_digit($id)) {
      $video = $videoRep->find($id);
    }
    if ($video === null) {
      $video = $videoRep->findOneBy(['shortName' => $id]);
    }

    if ($video === null) {
      $this->addFlash("danger", "Video not found.");
      return $this->redirectToRoute($this->homeRoute);
    }

    if ($video->getIsPrivate()) {
      if (!$videoHelper->checkPassword('', $video->getPassword())) {
        return $this->redirectToRoute('svc_video_pwd', ['id' => $video->getId(), 'path' => $request->attributes->get('_route')]);
      }
    }


    $video->incCalls();
    $this->getDoctrine()->getManager()->flush();

    return $this->render('@SvcVideo/video/run.html.twig', [
      'video' => $video,
      'enableLikes' => $this->enableLikes,
      'liked' => $likeHelper->isLiked(LikeHelper::SOURCE_VIDEO, $video->getId()),
      'hideNav' => $hideNav,
      'enableGroups' => $this->enableGroups
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

  /**
   * enter the password for a private video
   *
   */
  public function enterPwd(int $id, ?int $ot=1, Request $request, VideoHelper $videoHelper, VideoGroupRepository $videoGroupRep, VideoRepository $videoRep)
  {
    $form = $this->createForm(EnterPasswordType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      
      if ($ot == self::OBJ_TYPE_VGROUP) {
        $videoObj=$videoGroupRep->find($id);
      } else {
        $videoObj = $videoRep->find($id);
      }

      if ($videoHelper->checkPassword($form->get('plainPassword')->getData(), $videoObj->getPassword())) {
        $path = $request->query->get('path') ?? 'svc_video_run';
        try {
          $url = $this->generateUrl($path, ['id' => $videoObj->getId()]);
          return $this->redirect($url);
        } catch (RouteNotFoundException $e) {
          $this->addFlash('danger', 'Wrong parameter...');
          return $this->redirectToRoute('svc_video_list');
        }
      }
      $this->addFlash('danger', 'Wrong password');
    }

    return $this->renderForm('@SvcVideo/video/password.html.twig', [
      'form' => $form
    ]);
  }
}
