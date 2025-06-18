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

enum ObjectType: int
{
    case VIDEO = 1;
    case VGROUP = 2;

    public function label(): string
    {
        return match ($this) {
            ObjectType::VIDEO => 'video',
            ObjectType::VGROUP => 'video group',
        };
    }
}
