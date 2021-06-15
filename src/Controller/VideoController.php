<?php

namespace Svc\VideoBundle\Controller;

use DateTime;
use Svc\LikeBundle\Service\LikeHelper;
use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Form\EnterPasswordType;
use Svc\VideoBundle\Repository\VideoRepository;
use Svc\VideoBundle\Service\VideoGroupHelper;
use Svc\VideoBundle\Service\VideoHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;


class VideoController extends AbstractController
{
  private const SESS_ATTR_NAME = "svcv_password";

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
  public function list(?int $group = null, ?bool $hideNav = false, ?bool $hideGroups = false, VideoHelper $videoHelper, VideoGroupHelper $videoGroupHelper): Response
  {
    if ($hideGroups) {
      $this->enableGroups = false;
    }
    $groups = null;
    $currentGroup = null;

    if ($this->enableGroups) {
      $groups = $videoGroupHelper->getVideoGroups();
      if ($group) {
        $currentGroup = $videoGroupHelper->getVideoGroup($group);
        if ($currentGroup->getHideGroups()) {
          $this->enableGroups = false;
        }
        if ($currentGroup->getHideNav()) {
          $hideNav = true;
        }
      }
    }

    return $this->render('@SvcVideo/video/list.html.twig', [
      'videos' => $videoHelper->getVideoByGroup($group),
      'enableLikes' => $this->enableLikes,
      'enableGroups' => $this->enableGroups,
      'groups' => $groups,
      'currentGroup' => $currentGroup,
      'hideNav' => $hideNav,
    ]);
  }

  /**
   * run a video
   *
   * @param string $id numeric id or shortName
   * @return Response
   */
  public function run(string $id, ?bool $hideNav = false, LikeHelper $likeHelper, VideoRepository $videoRep, RequestStack $requestStack, VideoHelper $videoHelper): Response
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
      $plainPassword = $requestStack->getSession()->get(self::SESS_ATTR_NAME, null);
      if (!$plainPassword or $plainPassword != $videoHelper->decryptVideoPassword($video->getPassword())) {
        return $this->redirectToRoute('svc_video_pwd', ['id' => $video->getId()]);
      }
    }

    $video->incCalls();
    $this->getDoctrine()->getManager()->flush();

    return $this->render('@SvcVideo/video/run.html.twig', [
      'video' => $video,
      'enableLikes' => $this->enableLikes,
      'liked' => $likeHelper->isLiked(LikeHelper::SOURCE_VIDEO, $video->getId()),
      'hideNav' => $hideNav,
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
  public function enterPwd(Video $video, Request $request, VideoHelper $videoHelper, RequestStack $requestStack)
  {
    $form = $this->createForm(EnterPasswordType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $plainPassword = $form->get('plainPassword')->getData();
      $plainPasswordStored = $videoHelper->decryptVideoPassword($video->getPassword());
      if ($plainPassword == $plainPasswordStored) {
        $requestStack->getSession()->set(self::SESS_ATTR_NAME, $plainPassword);
        return $this->redirectToRoute('svc_video_run', ['id' => $video->getId()]);
      }

      $this->addFlash('danger', 'Wrong password');
    }

    return $this->renderForm('@SvcVideo/video_admin/password.html.twig', [
      'form' => $form
    ]);
  }
}
