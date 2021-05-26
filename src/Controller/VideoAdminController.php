<?php

namespace Svc\VideoBundle\Controller;

use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Svc\VideoBundle\Form\VideoType;

/**
 * @Route("/admin/video/{_locale}", requirements={"_locale": "%app.supported_locales%"})
 * @IsGranted("ROLE_ADMIN")
 */

class VideoAdminController extends AbstractController
{
  /**
   * @Route("/", name="video_admin_index", methods={"GET"})
   */
  public function index(VideoRepository $videoRepository): Response
  {
    return $this->render('@SvcVideo/video_admin/index.html.twig', [
      'videos' => $videoRepository->findAll(),
    ]);
  }

  /**
   * @Route("/new", name="video_admin_new", methods={"GET","POST"})
   */
  public function new(Request $request): Response
  {
    $video = new Video();
    $form = $this->createForm(VideoType::class, $video);
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

  /**
   * @Route("/{id}/edit", name="video_admin_edit", methods={"GET","POST"})
   */
  public function edit(Request $request, Video $video): Response
  {
    $form = $this->createForm(VideoType::class, $video);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('svc_video_admin_index');
    }

    return $this->render('@SvcVideo/video_admin/edit.html.twig', [
      'video' => $video,
      'form' => $form->createView(),
    ]);
  }

  /**
   * @Route("/{id}", name="video_admin_delete", methods={"post"})
   */
  public function delete(Request $request, Video $video): Response
  {
    if ($this->isCsrfTokenValid('delete' . $video->getId(), $request->request->get('_token'))) {
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->remove($video);
      $entityManager->flush();
    }

    return $this->redirectToRoute('@SvcVideo/video_admin_index');
  }
}
