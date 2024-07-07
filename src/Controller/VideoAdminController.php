<?php

namespace Svc\VideoBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Svc\LogBundle\Service\EventLog;
use Svc\LogBundle\Service\LogStatistics;
use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Entity\VideoGroup;
use Svc\VideoBundle\Enum\ObjectType;
use Svc\VideoBundle\Form\VideoType;
use Svc\VideoBundle\Repository\VideoGroupRepository;
use Svc\VideoBundle\Repository\VideoRepository;
use Svc\VideoBundle\Service\VideoGroupHelper;
use Svc\VideoBundle\Service\VideoHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class VideoAdminController extends AbstractController
{
  public function __construct(
    private readonly bool $enableShortNames,
    private readonly bool $enablePrivate,
    private readonly bool $enableGroups,
    private readonly bool $enablePagination,
    private readonly bool $enableTagging,
  ) {
  }

  public function index(VideoRepository $videoRepository, VideoGroupHelper $videoGroupHelper, Request $request): Response
  {
    $videoGroupHelper->initDefaultVideoGroup();
    $query = $request->query->getString('q');

    if ($this->enablePagination) {
      if ($query) {
        $queryBuilder = $videoRepository->qbFindBySearchQueryAdmin($query);
      } else {
        $queryBuilder = $videoRepository->cbAllVideos();
      }
      $videos = new Pagerfanta(new QueryAdapter($queryBuilder));
      $videos->setMaxPerPage(15);
      $videos->setCurrentPage($request->query->getInt('page', 1));
      $haveToPaginate = $videos->haveToPaginate();
    } else {
      $videos = $videoRepository->findAll();
      $haveToPaginate = false;
    }

    return $this->render('@SvcVideo/video_admin/index.html.twig', [
      'videos' => $videos,
      'enableShortNames' => $this->enableShortNames,
      'haveToPaginate' => $haveToPaginate,
      'enableTagging' => $this->enableTagging,
      'q' => $query,
    ]);
  }

  /**
   * create a new video.
   */
  public function new(Request $request, VideoGroupHelper $videoGroupHelper, VideoHelper $videoHelper, EntityManagerInterface $entityManager): Response
  {
    $video = new Video();
    $video->setVideoGroup($videoGroupHelper->getDefaultVideoGroup());
    $video->setUploadDate(new \DateTime());
    $form = $this->createForm(VideoType::class, $video, [
      'enableShortNames' => $this->enableShortNames,
      'enablePrivate' => $this->enablePrivate,
      'enableGroups' => $this->enableGroups,
      'enableTagging' => $this->enableTagging,
    ]);
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

    return $this->render('@SvcVideo/video_admin/new.html.twig', [
      'video' => $video,
      'form' => $form,
    ]);
  }

  /**
   * edit the video definition.
   */
  public function edit(Request $request, Video $video, VideoHelper $videoHelper, EntityManagerInterface $entityManager): Response
  {
    $video->setPlainPassword($videoHelper->getDecrypedPassword($video));

    $form = $this->createForm(VideoType::class, $video, [
      'enableShortNames' => $this->enableShortNames,
      'enablePrivate' => $this->enablePrivate,
      'enableGroups' => $this->enableGroups,
      'enableTagging' => $this->enableTagging,
    ]);
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

    return $this->render('@SvcVideo/video_admin/edit.html.twig', [
      'video' => $video,
      'form' => $form,
    ]);
  }

  public function delete(Request $request, Video $video, EntityManagerInterface $entityManager): Response
  {
    if ($this->isCsrfTokenValid('delete' . $video->getId(), $request->request->getString('_token'))) {
      $entityManager->remove($video);
      $entityManager->flush();
    }

    return $this->redirectToRoute('svc_video_admin_index');
  }

  /**
   * show statistics for a video.
   */
  public function stats(Video $video, LogStatistics $logStatistics, ChartBuilderInterface $chartBuilder): Response
  {
    $countries = $logStatistics->getCountriesForChartJS($video->getId(), ObjectType::VIDEO->value, EventLog::LEVEL_DATA);
    $countries['datasets'][0]['backgroundColor'] = ['#A3C408', '#86914E', '#F7D723', '#708AFA', '#085CC4'];
    $countries['datasets'][0]['borderColor'] = 'rgb(255, 255, 255)';

    $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
    $chart->setData($countries);
    $chart->setOptions([
      'responsive' => true,
      'plugins' => [
        'legend' => ['position' => 'bottom'],
        'title' => ['display' => true, 'text' => 'Countries'],
      ],
    ]);

    return $this->render('@SvcVideo/video_admin/stats.html.twig', [
      'video' => $video,
      'chart' => $chart,
      'sourceID' => $video->getId(),
      'sourceType' => ObjectType::VIDEO,
      'logLevel' => EventLog::LEVEL_DATA,
    ]);
  }

  /**
   * display statistics for all videos or video groups.
   *
   * @param bool $isVideo true: statistics for video, false: statistics for video groups
   */
  public function allStats(bool $isVideo, VideoRepository $videoRepo, VideoGroupRepository $videoGroupRepo, LogStatistics $logStatistics): Response
  {
    $videoStats = [];
    if ($isVideo) {
      $videos = $videoRepo->findAll();
      $videoType = ObjectType::VIDEO->value;
    } else {
      $videoType = ObjectType::VGROUP->value;
      $videos = $videoGroupRepo->findAll();
      $allVideoGroup = new VideoGroup();
      $allVideoGroup->setName('All videos');
      array_unshift($videos, $allVideoGroup);
    }

    $statistics = $logStatistics->pivotMonthly($videoType, EventLog::LEVEL_DATA, true);

    foreach ($videos as $video) {
      foreach ($statistics['data'] as $statistic) {
        if ($statistic['sourceID'] === $video->getId() or ($video->getId() === null and $statistic['sourceID'] === 0)) {
          $videoStats[$video->getId()] = $statistic;
          break;
        }
      }
    }

    return $this->render('@SvcVideo/video_admin/all_stats.html.twig', [
      'videos' => $videos,
      'statHeader' => $statistics['header'],
      'isVideo' => $isVideo,
      'stats' => $videoStats,
    ]);
  }
}
