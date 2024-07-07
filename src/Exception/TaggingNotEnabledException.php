<?php

namespace Svc\VideoBundle\Exception;

/**
 * @author Sven Vetter <dev@sv-systems.com>
 */
final class TaggingNotEnabledException extends \Exception
{
  /**
   * @var string
   */
  protected $message = 'Tagging is not enabled. You should not be able to invoke this method.';
}
