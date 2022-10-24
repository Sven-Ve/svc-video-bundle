<?php

declare(strict_types=1);

namespace Svc\VideoBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Entity\VideoGroup;

/**
 * testing the Video entity class.
 */
final class VideoGroupTest extends TestCase
{
  public function testEntityCreate(): void
  {
    $videoGroup = new VideoGroup();
    $this->assertInstanceOf(VideoGroup::class, $videoGroup, 'Create entity VideoGroup');
  }

  public function testNameSetAndGet(): void
  {
    $videoGroup = new VideoGroup();
    $videoGroup->setName('Test');
    $this->assertSame('Test', $videoGroup->getName(), 'Testing getName');
    $this->assertSame('Test', $videoGroup->getTitle(), 'Testing getTitle');
  }
}
