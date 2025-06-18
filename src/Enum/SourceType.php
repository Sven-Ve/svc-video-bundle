<?php

/*
 * This file is part of the SvcVideo bundle.
 *
 * (c) Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\VideoBundle\Enum;

enum SourceType: int
{
    case YOUTUBE = 0;
    case VIMEO = 1;

    public function label(): string
    {
        return match ($this) {
            SourceType::YOUTUBE => 'Youtube',
            SourceType::VIMEO => 'Vimeo',
        };
    }
}
