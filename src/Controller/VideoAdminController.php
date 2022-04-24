<?php

namespace Svc\VideoBundle\Controller;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Svc\LogBundle\Service\EventLog;
use Svc\LogBundle\Service\LogStatistics;
use Svc\VideoBundle\Entity\VideoGroup;
use Svc\VideoBundle\Form\VideoType;
use Svc\VideoBundle\Repository\VideoGroupRepository;
use Svc\VideoBundle\Service\VideoGroupHelper;
use Svc\VideoBundle\Service\VideoHelper;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

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
  public function new(Request $request, VideoGroupHelper $videoGroupHelper, VideoHelper $videoHelper, EntityManagerInterface $entityManager): Response
  {
    $video = new Video();
    $video->setVideoGroup($videoGroupHelper->getDefaultVideoGroup());
    $video->setUploadDate(new DateTime());
    $form = $this->createForm(VideoType::class, $video, ['enableShortNames' => $this->enableShortNames, 'enablePrivate' => $this->enablePrivate, 'enableGroups' => $this->enableGroups]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $video->setPassword($videoHelper->getEncPassword($video));

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
  public function edit(Request $request, Video $video, VideoHelper $videoHelper, EntityManagerInterface $entityManager): Response
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
      $entityManager->flush();

      return $this->redirectToRoute('svc_video_admin_index');
    }

    return $this->renderForm('@SvcVideo/video_admin/edit.html.twig', [
      'video' => $video,
      'form' => $form,
    ]);
  }

  public function delete(Request $request, Video $video, EntityManagerInterface $entityManager): Response
  {
    if ($this->isCsrfTokenValid('delete' . $video->getId(), $request->request->get('_token'))) {
      $entityManager->remove($video);
      $entityManager->flush();
    }

    return $this->redirectToRoute('svc_video_admin_index');
  }

  /**
   * show statistics for a video
   */
  public function stats(Video $video, LogStatistics $logStatistics, ChartBuilderInterface $chartBuilder): Response
  {
    $countries = $logStatistics->getCountriesForChartJS($video->getId(), VideoController::OBJ_TYPE_VIDEO, EventLog::LEVEL_DATA);
    $countries["datasets"][0]["backgroundColor"] = ['#A3C408', '#86914E', '#F7D723', '#708AFA', '#085CC4'];
    $countries["datasets"][0]["borderColor"] = 'rgb(255, 255, 255)';

    $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
    $chart->setData($countries);
    $chart->setOptions([
      "responsive" => true,
      "legend" => ["position" => "bottom"],
      "title" => ["display" => true, "text"=>"Countries"]
    ]);

    return $this->render('@SvcVideo/video_admin/stats.html.twig', [
      'video' => $video,
      'chart' => $chart,
      'sourceID' => $video->getId(),
      'sourceType' => VideoController::OBJ_TYPE_VIDEO,
      'logLevel' => EventLog::LEVEL_DATA
    ]);
  }

  /**
   * display statistics for all videos or video groups
   *
   * @param bool $isVideo true: statistics for video, false: statistics for video groups
   * @param LogStatistics $logStatistics
   * @return Response
   */
  public function allStats(bool $isVideo, VideoRepository $videoRepo, VideoGroupRepository $videoGroupRepo, LogStatistics $logStatistics): Response
  {

    if ($isVideo) {
      $videos = $videoRepo->findAll();
      $videoType = VideoController::OBJ_TYPE_VIDEO;
    } else {
      $videoType = VideoController::OBJ_TYPE_VGROUP;
      $videos = $videoGroupRepo->findAll();
      $allVideoGroup  = new VideoGroup;
      $allVideoGroup->setName("All videos");
      array_unshift($videos, $allVideoGroup);
    }

    $statistics = $logStatistics->pivotMonthly($videoType, EventLog::LEVEL_DATA);

    foreach ($videos as $video) {
      foreach ($statistics['data'] as $statistic) {
        if ($statistic['sourceID'] === $video->getId() or ($video->getId() === null and $statistic['sourceID']===0)) {
          $video->statistics = $statistic;
          break;
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
