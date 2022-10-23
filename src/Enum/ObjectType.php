<?php

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
