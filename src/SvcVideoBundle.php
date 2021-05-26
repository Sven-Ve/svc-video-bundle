<?php

namespace Svc\VideoBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SvcVideoBundle extends Bundle {

  public function getPath(): string
  {
      return \dirname(__DIR__);
  }
}