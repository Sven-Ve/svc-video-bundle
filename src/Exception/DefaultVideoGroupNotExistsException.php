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

class DefaultVideoGroupNotExistsException extends \Exception
{
    /**
     * @var string
     */
    protected $message = 'Default video group not found. Please initialize the app.';
}
