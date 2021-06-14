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
      $plainPassword = $form->get('plainPassword')->getData();
      if ($plainPassword and $video->getIsPrivate()) {
        $video->setPassword($videoHelper->encryptVideoPassword($plainPassword));
      }

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


  public function edit(Request $request, Video $video, VideoHelper $videoHelper): Response
  {
    if ($video->getIsPrivate() and $video->getPassword()) {
      $plainPassword = $videoHelper->decryptVideoPassword($video->getPassword());
    } else {
      $plainPassword = null;
    }

    $form = $this->createForm(VideoType::class, $video, ['enableShortNames' => $this->enableShortNames, 'plainPassword' => $plainPassword]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $plainPassword = $form->get('plainPassword')->getData();
      if ($plainPassword and $video->getIsPrivate()) {
        $video->setPassword($videoHelper->encryptVideoPassword($plainPassword));
      }

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
}
