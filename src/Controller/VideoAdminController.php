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
  private $videoHelper;
  public function __construct(bool $enableShortNames, VideoHelper $videoHelper)
  {
    $this->enableShortNames = $enableShortNames;
    $this->videoHelper = $videoHelper;
  }

  public function index(VideoRepository $videoRepository, VideoGroupHelper $videoGroupHelper): Response
  {
    $videoGroupHelper->initDefaultVideoGroup();

    return $this->render('@SvcVideo/video_admin/index.html.twig', [
      'videos' => $videoRepository->findAll(),
      'enableShortNames' => $this->enableShortNames
    ]);
  }

  public function new(Request $request, VideoGroupHelper $videoGroupHelper): Response
  {
    $video = new Video();
    $video->setVideoGroup($videoGroupHelper->getDefaultVideoGroup());
    $form = $this->createForm(VideoType::class, $video, ['enableShortNames' => $this->enableShortNames]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
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


  public function edit(Request $request, Video $video): Response
  {
    $form = $this->createForm(VideoType::class, $video, ['enableShortNames' => $this->enableShortNames]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      if (!$video->isThumbnailUrl()) {
        $video->setThumbnailUrl($this->videoHelper->getThumbnailUrl($video));
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
