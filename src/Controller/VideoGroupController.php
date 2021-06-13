<?php

namespace Svc\VideoBundle\Controller;

use Svc\VideoBundle\Entity\VideoGroup;
use Svc\VideoBundle\Form\VideoGroupType;
use Svc\VideoBundle\Repository\VideoGroupRepository;
use Svc\VideoBundle\Service\VideoGroupHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class VideoGroupController extends AbstractController
{

  public function index(VideoGroupRepository $videoGroupRepository, VideoGroupHelper $videoGroupHelper): Response
  {
    $videoGroupHelper->initDefaultVideoGroup();

    return $this->render('@SvcVideo/video_group/index.html.twig', [
      'video_groups' => $videoGroupRepository->findAll(),
    ]);
  }

  public function new(Request $request): Response
  {
    $videoGroup = new VideoGroup();
    $form = $this->createForm(VideoGroupType::class, $videoGroup);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($videoGroup);
      $entityManager->flush();

      return $this->redirectToRoute('svc_video_group_index');
    }

    return $this->render('@SvcVideo/video_group/new.html.twig', [
      'video_group' => $videoGroup,
      'form' => $form->createView(),
    ]);
  }


  public function edit(Request $request, VideoGroup $videoGroup): Response
  {
    $form = $this->createForm(VideoGroupType::class, $videoGroup);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('svc_video_group_index');
    }

    return $this->render('@SvcVideo/video_group/edit.html.twig', [
      'video_group' => $videoGroup,
      'form' => $form->createView(),
    ]);
  }

  public function delete(Request $request, VideoGroup $videoGroup): Response
  {
    if ($this->isCsrfTokenValid('delete' . $videoGroup->getId(), $request->request->get('_token'))) {
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->remove($videoGroup);
      $entityManager->flush();
    }

    return $this->redirectToRoute('svc_video_group_index');
  }
}
