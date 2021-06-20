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
use Svc\VideoBundle\Service\VideoHelper;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class VideoGroupController extends AbstractController
{

  private $enableShortNames;
  private $enablePrivate;
  public function __construct(bool $enableShortNames, bool $enablePrivate)
  {
    $this->enableShortNames = $enableShortNames;
    $this->enablePrivate = $enablePrivate;
  }

  public function index(VideoGroupRepository $videoGroupRepository, VideoGroupHelper $videoGroupHelper): Response
  {
    $videoGroupHelper->initDefaultVideoGroup();

    return $this->render('@SvcVideo/video_group/index.html.twig', [
      'video_groups' => $videoGroupRepository->findAll(),
    ]);
  }

  public function new(Request $request, VideoHelper $videoHelper): Response
  {
    $videoGroup = new VideoGroup();
    $form = $this->createForm(VideoGroupType::class, $videoGroup, ['enableShortNames' => $this->enableShortNames, 'enablePrivate' => $this->enablePrivate]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $videoGroup->setPassword($videoHelper->getEncPassword($videoGroup));

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($videoGroup);
      $entityManager->flush();

      return $this->redirectToRoute('svc_video_group_index');
    }

    return $this->renderForm('@SvcVideo/video_group/new.html.twig', [
      'video_group' => $videoGroup,
      'form' => $form,
    ]);
  }

  /**
   * edit the video group
   */
  public function edit(Request $request, VideoGroup $videoGroup, VideoHelper $videoHelper): Response
  {
    $videoGroup->setPlainPassword($videoHelper->getDecrypedPassword($videoGroup));

    $form = $this->createForm(VideoGroupType::class, $videoGroup, ['enableShortNames' => $this->enableShortNames, 'enablePrivate' => $this->enablePrivate]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $videoGroup->setPassword($videoHelper->getEncPassword($videoGroup));

      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('svc_video_group_index');
    }

    return $this->renderForm('@SvcVideo/video_group/edit.html.twig', [
      'video_group' => $videoGroup,
      'form' => $form,
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
