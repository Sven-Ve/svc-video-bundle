<?php

namespace Svc\VideoBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use Svc\VideoBundle\Enum\SourceType;

class SourceTypeTest extends TestCase
{
  public function testEnumSourceType(): void
  {
    $sourceType1 = SourceType::VIMEO;
    $this->assertSame($sourceType1, SourceType::VIMEO, 'Sourcetype has to be Vimeo');
    $this->assertSame($sourceType1->value, 1, 'Sourcetype value has to be 1 for Vimeo');
    $this->assertSame($sourceType1->label(), 'Vimeo', 'Sourcetype label has to be Vimeo for Vimeo');

    $sourceType2 = SourceType::YOUTUBE;
    $this->assertSame($sourceType2, SourceType::YOUTUBE, 'Sourcetype has to be Youtube');
    $this->assertSame($sourceType2->value, 0, 'Sourcetype value has to be 0 for Youtube');
    $this->assertSame($sourceType2->label(), 'Youtube', 'Sourcetype label has to be Youtube for Youtube');
  }
}
