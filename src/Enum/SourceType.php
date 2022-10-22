<?php

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
