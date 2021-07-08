<?php

namespace Svc\VideoBundle\Controller;

use Svc\VideoBundle\Service\VideoGroupHelper;
use Symfony\Component\HttpFoundation\Response;

trait VideoShortCallTrait
{

  /**
   * @Route("/hn/{id}", name="svc_video_short_runHideNav")
   */
  public function shortRunHideNav($id): Response
  {
    return $this->redirectToRoute('svc_video_run_hn', ['id' => $id], 303);
  }

  /**
   * @Route("/{id}", name="svc_video_short_run", requirements={"id"="\d+"})
   */
  public function shortRun($id): Response
  {
    return $this->redirectToRoute('svc_video_run', ['id' => $id], 303);
  }

  /**
   * @Route("/v/{id}", name="svc_video_short_run1")
   */
  public function shortRun1($id): Response
  {
    return $this->redirectToRoute('svc_video_run', ['id' => $id], 303);
  }

  /**
   * @Route("/g/{group}", name="svc_video_shortGroup")
   */
  public function shortGroup($group, VideoGroupHelper $videoGroupHelper): Response
  {
    $id = $videoGroupHelper->getVideoGroupIDbyShortName($group);
    return $this->redirectToRoute('svc_video_list', ['id' => $id], 303);
  }
}
