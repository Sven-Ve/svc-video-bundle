<?php

namespace Svc\VideoBundle\Exception;

class DefaultVideoGroupNotExistsException extends \Exception
{
  /**
   * @var string
   */
  protected $message = 'Default video group not found. Please initialize the app.';
}
