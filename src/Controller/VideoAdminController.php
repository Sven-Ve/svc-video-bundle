<?php

namespace Svc\VideoBundle\Controller;

use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Svc\VideoBundle\Form\VideoType;
use Svc\VideoBundle\Service\VideoGroupHelper;
use Svc\VideoBundle\Service\VideoHelper;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class VideoAdminController extends AbstractController
{
  private $enableShortNames;
  public function __construct(bool $enableShortNames)
  {
    $this->enableShortNames = $enableShortNames;
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
    $form = $this->createForm(VideoType::class, $video, ['enableShortNames' => $this->enableShortNames]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $thumbnailUrl=$videoHelper->getThumbnailUrl($video);
      if ($thumbnailUrl) {
        $video->setThumbnailUrl($thumbnailUrl);
        $video->setThumbnailPath($videoHelper->copyThumbnail($video));
      }
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($video);
      $entityManager->flush();

      return $this->redirectToRoute('svc_video_admin_index');
    }

    return $this->render('@SvcVideo/video_admin/new.html.twig', [
      'video' => $video,
      'form' => $form->createView(),
    ]);
  }


  public function edit(Request $request, Video $video, VideoHelper $videoHelper): Response
  {
    $form = $this->createForm(VideoType::class, $video, ['enableShortNames' => $this->enableShortNames]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      if (!$video->isThumbnailPath()) {
        if (!$video->isThumbnailUrl()) {
          $video->setThumbnailUrl($videoHelper->getThumbnailUrl($video));
        }
        if ($video->isThumbnailUrl()) {
          $video->setThumbnailPath($videoHelper->copyThumbnail($video));
        }  
      }
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('svc_video_admin_index');
    }

    return $this->render('@SvcVideo/video_admin/edit.html.twig', [
      'video' => $video,
      'form' => $form->createView(),
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
}
