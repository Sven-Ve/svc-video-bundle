<?php

namespace Svc\VideoBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

trait VideoShortCallTrait
{

  /**
   * @Route("/hn/{id}", name="svc_video_short_runHideNav")
   */
  public function shortRunHideNav($id): Response
  {
    return $this->redirectToRoute('svc_video_run_hn', ['id' => $id]);
  }

  /**
   * @Route("/{id}", name="svc_video_short_run", requirements={"id"="\d+"})
   */
  public function shortRun($id): Response
  {
    return $this->redirectToRoute('svc_video_run', ['id' => $id]);
  }


  /**
   * @Route("/v/{id}", name="svc_video_short_run1")
   */
  public function shortRun1($id): Response
  {
    return $this->redirectToRoute('svc_video_run', ['id' => $id]);
  }
}
