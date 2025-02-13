<?php

namespace Svc\VideoBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Svc\LikeBundle\Service\LikeHelper;
use Svc\LogBundle\Enum\LogLevel;
use Svc\LogBundle\Service\EventLog;
use Svc\LogBundle\Service\LogAppConstants;
use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Enum\ObjectType;
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
  public function __construct(
    private readonly bool $enableLikes,
    private readonly bool $enableGroups,
    private readonly bool $enableShortNames,
    private readonly bool $enableVideoSort,
    private readonly string $homeRoute,
    private readonly bool $enableTagging,
    private readonly EntityManagerInterface $entityManager)
  {
  }

  /**
   * list videos.
   */
  public function list(
    VideoHelper $videoHelper,
    VideoGroupHelper $videoGroupHelper,
    VideoRepository $videoRep,
    Request $request,
    EventLog $eventLog,
    ?int $id = null): Response
  {
    $sort = $request->query->getInt('sort');
    $query = $request->query->getString('q');

    $hideNav = false;
    $hideGroups = false;

    $groups = null;
    $currentGroup = null;

    if ($this->enableGroups) {
      $groups = $videoGroupHelper->getVideoGroups(true);
      if ($id) {
        $currentGroup = $videoGroupHelper->getVideoGroup($id);
        if (!$currentGroup) {
          return $this->redirectToRoute('svc_video_list');
        }
        $hideGroups = $currentGroup->getHideGroups();
        $hideNav = $currentGroup->getHideNav();

        if ($currentGroup->getIsPrivate()) {
          if (!$videoHelper->checkPassword('', $currentGroup->getPassword())) {
            return $this->redirectToRoute('svc_video_pwd', ['id' => $currentGroup->getId(), 'ot' => ObjectType::VGROUP->value, 'path' => $request->attributes->get('_route')]);
          }
        }
      }
    } else {
      $hideGroups = true;
    }

    $eventLog->writeLog($currentGroup ? $currentGroup->getId() : 0, ObjectType::VGROUP->value);

    if ($id === null and $query != null) {
      $videos = $videoRep->findBySearchQuery($query);
    } else {
      $videos = $videoHelper->getVideoByGroup($id, $sort);
    }

    return $this->render('@SvcVideo/video/list.html.twig', [
      'videos' => $videos,
      'enableLikes' => $this->enableLikes,
      'groups' => $groups,
      'currentGroup' => $currentGroup,
      'hideGroups' => $hideGroups,
      'hideNav' => $hideNav,
      'enableVideoSort' => $this->enableVideoSort,
      'sortOpts' => VideoRepository::SORT_FIELDS,
      'currentSort' => $sort,
      'copyUrl' => $videoGroupHelper->generateVideoGroupUrl($currentGroup, $sort),
      'enableTagging' => $this->enableTagging,
      'q' => $query,
    ]);
  }

  /**
   * run a video.
   *
   * @param string $id numeric id or shortName
   */
  public function run(string $id, LikeHelper $likeHelper, VideoRepository $videoRep, Request $request, VideoHelper $videoHelper, EventLog $eventLog, ?bool $hideNav = false): Response
  {
    $video = null;
    if (ctype_digit($id)) {
      $video = $videoRep->find($id);
    }
    if ($video === null and $this->enableShortNames) {
      $video = $videoRep->findOneBy(['shortName' => $id]);
    }

    if ($video === null) {
      $this->addFlash('danger', 'Video not found.');

      return $this->redirectToRoute($this->homeRoute);
    }

    $currentRoute = $request->attributes->get('_route');
    if ($video->getIsPrivate() and $video->getPassword()) {
      if (!$videoHelper->checkPassword('', $video->getPassword())) {
        return $this->redirectToRoute('svc_video_pwd', ['id' => $video->getId(), 'ot' => ObjectType::VIDEO->value, 'path' => $currentRoute]);
      }
    }

    $video->incCalls();
    $this->entityManager->flush();

    if ($this->enableGroups and !$hideNav) {
      $hideNav = $video->getVideoGroup()->getHideNav();
    }

    $eventLog->writeLog($video->getId(), ObjectType::VIDEO->value);

    return $this->render('@SvcVideo/video/run.html.twig', [
      'video' => $video,
      'enableLikes' => $this->enableLikes,
      'liked' => $likeHelper->isLiked(LikeHelper::SOURCE_VIDEO, $video->getId()),
      'hideNav' => $hideNav,
      'enableGroups' => $this->enableGroups,
      'copyUrl' => $videoHelper->generateVideoUrl($video, $currentRoute),
      'enableTagging' => $this->enableTagging,
    ]);
  }

  /**
   * increase the like count.
   */
  public function incLikes(Video $video, LikeHelper $likeHelper): Response
  {
    $response = new JsonResponse();
    $cookieName = null;

    if ($likeHelper->addLike(LikeHelper::SOURCE_VIDEO, $video->getId(), null, $cookieName)) {
      if ($cookieName) {
        $response->headers->setCookie(Cookie::create($cookieName, '1', new \DateTime('+1 week')));
      }

      $newValue = $video->incLikes();
      $this->entityManager->flush();

      $response->setData(['likes' => $newValue]);
    } else {
      $response->setStatusCode(422);
    }

    return $response;
  }

  /**
   * enter the password for a private video.
   */
  public function enterPwd(
    int $id,
    Request $request,
    VideoHelper $videoHelper,
    VideoGroupRepository $videoGroupRep,
    VideoRepository $videoRep,
    EventLog $eventLog,
    ?ObjectType $ot = ObjectType::VIDEO): Response
  {
    $ot ??= ObjectType::VIDEO;
    $form = $this->createForm(EnterPasswordType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      if ($ot == ObjectType::VGROUP) {
        $videoObj = $videoGroupRep->find($id);
      } else {
        $videoObj = $videoRep->find($id);
      }

      if (!$videoObj) {
        $eventLog->writeLog($id, LogAppConstants::LOG_TYPE_HACKING_ATTEMPT, level: LogLevel::ERROR, message: 'hacking url: ' . $request->getUri());

        $this->addFlash('danger', 'Wrong parameter, please do not modify the url...');

        return $this->redirectToRoute('svc_video_list');
      }

      if ($videoHelper->checkPassword($form->get('plainPassword')->getData(), $videoObj->getPassword())) {
        $path = $request->query->getString('path');
        if (!$path) {
          $path = 'svc_video_run';
        }
        try {
          $url = $this->generateUrl($path, ['id' => $videoObj->getId()]);
          $eventLog->writeLog($id, $ot->value, LogLevel::DEBUG, message: 'correct password');

          return $this->redirect($url);
        } catch (RouteNotFoundException) {
          $this->addFlash('danger', 'Wrong parameter...');

          return $this->redirectToRoute('svc_video_list');
        }
      }
      $eventLog->writeLog($id, $ot->value, LogLevel::WARN, message: 'wrong password');
      $this->addFlash('danger', 'Wrong password');
    }

    $eventLog->writeLog($id, $ot->value, LogLevel::DEBUG, message: 'enter password');

    return $this->render('@SvcVideo/video/password.html.twig', [
      'form' => $form,
    ]);
  }

  /**
   * video statistics (for a video).
   */
  public function videoStats(VideoHelper $videoHelper): Response
  {
    return $this->render('@SvcVideo/video/stats.html.twig', [
      'hideGroups' => !$this->enableGroups,
      'stats' => $videoHelper->getVideoStats(),
    ]);
  }
}
