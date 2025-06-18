<?php

/*
 * This file is part of the SvcVideo bundle.
 *
 * (c) Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
