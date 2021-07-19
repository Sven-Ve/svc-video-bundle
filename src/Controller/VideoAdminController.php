<?php

namespace Svc\VideoBundle\Controller;

use DateTime;
use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Svc\LogBundle\Service\EventLog;
use Svc\LogBundle\Service\LogStatistics;
use Svc\VideoBundle\Form\VideoType;
use Svc\VideoBundle\Repository\VideoGroupRepository;
use Svc\VideoBundle\Service\VideoGroupHelper;
use Svc\VideoBundle\Service\VideoHelper;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class VideoAdminController extends AbstractController
{
  private $enableShortNames;
  private $enablePrivate;
  private $enableGroups;
  public function __construct(bool $enableShortNames, bool $enablePrivate, bool $enableGroups)
  {
    $this->enableShortNames = $enableShortNames;
    $this->enablePrivate = $enablePrivate;
    $this->enableGroups = $enableGroups;
  }

  public function index(VideoRepository $videoRepository, VideoGroupHelper $videoGroupHelper): Response
  {
    $videoGroupHelper->initDefaultVideoGroup();

    return $this->render('@SvcVideo/video_admin/index.html.twig', [
      'videos' => $videoRepository->findAll(),
      'enableShortNames' => $this->enableShortNames
    ]);
  }

  /**
   * create a new video
   */
  public function new(Request $request, VideoGroupHelper $videoGroupHelper, VideoHelper $videoHelper): Response
  {
    $video = new Video();
    $video->setVideoGroup($videoGroupHelper->getDefaultVideoGroup());
    $video->setUploadDate(new DateTime());
    $form = $this->createForm(VideoType::class, $video, ['enableShortNames' => $this->enableShortNames, 'enablePrivate' => $this->enablePrivate, 'enableGroups' => $this->enableGroups]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $video->setPassword($videoHelper->getEncPassword($video));

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($video);
      $entityManager->flush(); // save first because we need the id

      $videoHelper->getVideoMetadata($video);
      if ($video->getThumbnailUrl()) {
        $video->setThumbnailPath($videoHelper->copyThumbnail($video));
      }
      $entityManager->flush();

      return $this->redirectToRoute('svc_video_admin_index');
    }

    return $this->renderForm('@SvcVideo/video_admin/new.html.twig', [
      'video' => $video,
      'form' => $form,
    ]);
  }


  /**
   * edit the video definition
   */
  public function edit(Request $request, Video $video, VideoHelper $videoHelper): Response
  {
    $video->setPlainPassword($videoHelper->getDecrypedPassword($video));


    $form = $this->createForm(VideoType::class, $video, ['enableShortNames' => $this->enableShortNames, 'enablePrivate' => $this->enablePrivate, 'enableGroups' => $this->enableGroups]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $video->setPassword($videoHelper->getEncPassword($video));

      if (!$video->isThumbnailPath()) {
        if (!$video->isThumbnailUrl()) {
          $videoHelper->getVideoMetadata($video);
        }
        if ($video->isThumbnailUrl()) {
          $video->setThumbnailPath($videoHelper->copyThumbnail($video));
        }
      }
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('svc_video_admin_index');
    }

    return $this->renderForm('@SvcVideo/video_admin/edit.html.twig', [
      'video' => $video,
      'form' => $form,
    ]);
  }

  public function delete(Request $request, Video $video): Response
  {
    if ($this->isCsrfTokenValid('delete' . $video->getId(), $request->request->get('_token'))) {
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->remove($video);
      $entityManager->flush();
    }

    return $this->redirectToRoute('svc_video_admin_index');
  }

  /**
   * show statistics for a video
   */
  public function stats1(Video $video, LogStatistics $logStatistics): Response
  {
    return $this->render('@SvcVideo/video_admin/stats.html.twig', [
      'video' => $video,
      'logData' => $logStatistics->reportOneId($video->getId(), VideoController::OBJ_TYPE_VIDEO)
    ]);
  }

  /**
   * display statistics for all videos or video groups
   *
   * @param bool $isVideo true: statistics for video, false: statistics for video groups
   * @param LogStatistics $logStatistics
   * @return void
   */
  public function allStats(bool $isVideo, VideoRepository $videoRepo, VideoGroupRepository $videoGroupRepo, LogStatistics $logStatistics): Response
  {

    if ($isVideo) {
      $videos = $videoRepo->findAll();
      $videoType = VideoController::OBJ_TYPE_VIDEO;
    } else {
      $videoType = VideoController::OBJ_TYPE_VGROUP;
      $videos = $videoGroupRepo->findAll();
    }

    $statistics = $logStatistics->pivotMonthly($videoType, EventLog::LEVEL_DATA);

    foreach ($videos as $video) {
      foreach ($statistics['data'] as $statistic) {
        if ($statistic['sourceID'] == $video->getId()) {
          $video->statistics = $statistic;
          continue;
        }
      }
    }

    return $this->render('@SvcVideo/video_admin/all_stats.html.twig', [
      'videos' => $videos,
      'statHeader' => $statistics['header'],
      'isVideo' => $isVideo,
    ]);
  }
}
