<?php

namespace Svc\VideoBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Entity\VideoGroup;
use Svc\VideoBundle\Enum\ObjectType;
use Svc\VideoBundle\Repository\VideoGroupRepository;
use Svc\VideoBundle\Repository\VideoRepository;
use Svc\VideoBundle\Service\LogDataProvider;

class LogDataProviderTest extends TestCase
{
  public function testServiceCreate(): void
  {
    $mockVideo = $this->createMock(Video::class);
    $mockVideo->method('getId')
      ->willReturn(1);
    $mockVideo->method('getTitle')
      ->willReturn('Test Video');

    $mockVideoGroup = $this->createMock(VideoGroup::class);
    $mockVideoGroup->method('getId')
      ->willReturn(1);
    $mockVideoGroup->method('getTitle')
      ->willReturn('Test Video Group');

    $mockVideoRep = $this->createMock(VideoRepository::class);
    $mockVideoRep->method('findAll')
      ->willReturn([$mockVideo]);

    $mockVideoGroupRep = $this->createMock(VideoGroupRepository::class);
    $mockVideoGroupRep->method('findAll')
      ->willReturn([$mockVideoGroup]);

    $ldProvider = new LogDataProvider($mockVideoRep, $mockVideoGroupRep);

    $this->assertInstanceOf(LogDataProvider::class, $ldProvider);

    // Video
    $this->assertSame('1', $ldProvider->getSourceIDText(1), 'Get SourceID for empty sourceType');
    $this->assertSame('Test Video', $ldProvider->getSourceIDText(1, ObjectType::VIDEO->value), 'Get Video text for existing sourceID');
    $this->assertSame('2', $ldProvider->getSourceIDText(2, ObjectType::VIDEO->value), 'Get Video text for not existing sourceID');

    // Video group
    $this->assertSame('Test Video Group', $ldProvider->getSourceIDText(1, ObjectType::VGROUP->value), 'Get Video group text for existing sourceID');
    $this->assertSame('2', $ldProvider->getSourceIDText(2, ObjectType::VGROUP->value), 'Get Video group text for not existing sourceID');
    $this->assertSame('All videos', $ldProvider->getSourceIDText(0, ObjectType::VGROUP->value), 'Get Video group text for for group 0');
  }
}
