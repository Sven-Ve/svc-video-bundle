<?php

namespace Svc\VideoBundle\Controller;

use Svc\VideoBundle\Service\VideoGroupHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @phpstan-ignore trait.unused */
trait VideoShortCallTrait
{
  #[Route(path: '/hn/{id}', name: 'svc_video_short_runHideNav')]
  public function shortRunHideNav($id): Response
  {
    return $this->redirectToRoute('svc_video_run_hn', ['id' => $id], 303);
  }

  #[Route(path: '/{id}', name: 'svc_video_short_run', requirements: ['id' => "\d+"])]
  public function shortRun($id): Response
  {
    return $this->redirectToRoute('svc_video_run', ['id' => $id], 303);
  }

  #[Route(path: '/v/{id}', name: 'svc_video_short_run1')]
  public function shortRun1($id): Response
  {
    return $this->redirectToRoute('svc_video_run', ['id' => $id], 303);
  }

  #[Route(path: '/g/{group}', name: 'svc_video_shortGroup')]
  public function shortGroup($group, VideoGroupHelper $videoGroupHelper, Request $request): Response
  {
    $sort = $request->query->get('sort') ?? 0;
    $id = $videoGroupHelper->getVideoGroupIDbyShortName($group);

    return $this->redirectToRoute('svc_video_list', ['id' => $id, 'sort' => $sort], 303);
  }
}
