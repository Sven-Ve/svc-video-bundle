<?php

namespace Svc\VideoBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Svc\VideoBundle\Entity\Tag;
use Svc\VideoBundle\Exception\TaggingNotEnabledException;
use Svc\VideoBundle\Form\TagType;
use Svc\VideoBundle\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TagAdminController extends AbstractController
{
  /**
   * @throws TaggingNotEnabledException
   */
  public function __construct(private bool $enableTagging)
  {
    if (!$this->enableTagging) {
      throw new TaggingNotEnabledException();
    }
  }

  public function index(TagRepository $tagRepository): Response
  {
    return $this->render('@SvcVideo/tag/index.html.twig', [
      'tags' => $tagRepository->findAll(),
    ]);
  }

  public function new(Request $request, EntityManagerInterface $entityManager): Response
  {
    $tag = new Tag();
    $form = $this->createForm(TagType::class, $tag);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->persist($tag);
      $entityManager->flush();

      return $this->redirectToRoute('svc_tag_admin_index');
    }

    return $this->render('@SvcVideo/tag/new.html.twig', [
      'tag' => $tag,
      'form' => $form,
    ]);
  }

  /**
   * edit a tag.
   */
  public function edit(Request $request, Tag $tag, EntityManagerInterface $entityManager): Response
  {
    $form = $this->createForm(TagType::class, $tag);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->flush();

      return $this->redirectToRoute('svc_tag_admin_index');
    }

    return $this->render('@SvcVideo/tag/edit.html.twig', [
      'tag' => $tag,
      'form' => $form,
    ]);
  }

  public function delete(Request $request, Tag $tag, EntityManagerInterface $entityManager): Response
  {
    if ($this->isCsrfTokenValid('delete' . $tag->getId(), $request->request->get('_token'))) {
      $entityManager->remove($tag);
      $entityManager->flush();
    }

    return $this->redirectToRoute('svc_tag_admin_index');
  }
}
