<?php

declare(strict_types=1);

namespace Svc\VideoBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Enum\SourceType;

/**
 * testing the Video entity class.
 */
final class VideoTest extends TestCase
{
  public function testUploadDate(): void
  {
    $video = new Video();
    $this->assertNull($video->getUploadDate(), 'Upload date has to be null initially');

    $currDate = new \DateTime();
    $video->setUploadDate($currDate);
    $this->assertEquals($currDate, $video->getUploadDate(), 'UploadDate has to be now');
  }

  public function testLikesSetAndGet(): void
  {
    $video = new Video();
    $this->assertSame(0, $video->getLikes(), 'Likes has to be 0 initially');

    $video->incLikes();
    $this->assertSame(1, $video->getLikes(), 'Likes has to be 1 after first inc');

    $video->setLikes(10);
    $this->assertSame(10, $video->getLikes(), 'Likes has to be 10 after set to 10');
  }

  public function testCallsSetAndGet(): void
  {
    $video = new Video();
    $this->assertSame(0, $video->getCalls(), 'Calls has to be 0 initially');

    $video->incCalls();
    $this->assertSame(1, $video->getCalls(), 'Calls has to be 1 after first inc');

    $video->setCalls(10);
    $this->assertSame(10, $video->getCalls(), 'Calls has to be 10 after set to 10');
  }

  public function testRatioGet(): void
  {
    $video = new Video();
    $this->assertSame('16x9', $video->getRatio(), 'Ratio has to be 16x9 initially');
  }

  public function testSourceTypeSetAndGet(): void
  {
    $video = new Video();
    $this->assertSame(SourceType::VIMEO, $video->getSourceType(), 'Sourcetype has to be Vimeo initially');

    $video->setSourceType(SourceType::YOUTUBE);
    $this->assertSame(SourceType::YOUTUBE, $video->getSourceType(), 'Sourcetype has to be Youtube after set to Youtube');
  }
}
