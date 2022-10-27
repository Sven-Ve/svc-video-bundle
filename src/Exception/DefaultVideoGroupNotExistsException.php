<?php

namespace Svc\VideoBundle\Exception;

class DefaultVideoGroupNotExistsException extends \Exception
{
  protected $message = 'Default video group not found. Please initialize the app.';
}
