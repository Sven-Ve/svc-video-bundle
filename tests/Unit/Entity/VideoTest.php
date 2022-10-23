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
    $this->assertEquals($video->getUploadDate(), $currDate, 'UploadDate has to be now');
  }

  public function testLikesSetAndGet(): void
  {
    $video = new Video();
    $this->assertSame($video->getLikes(), 0, 'Likes has to be 0 initially');

    $video->incLikes();
    $this->assertSame($video->getLikes(), 1, 'Likes has to be 1 after first inc');

    $video->setLikes(10);
    $this->assertSame($video->getLikes(), 10, 'Likes has to be 10 after set to 10');
  }

  public function testCallsSetAndGet(): void
  {
    $video = new Video();
    $this->assertSame($video->getCalls(), 0, 'Calls has to be 0 initially');

    $video->incCalls();
    $this->assertSame($video->getCalls(), 1, 'Calls has to be 1 after first inc');

    $video->setCalls(10);
    $this->assertSame($video->getCalls(), 10, 'Calls has to be 10 after set to 10');
  }

  public function testRatioGet(): void
  {
    $video = new Video();
    $this->assertSame($video->getRatio(), '16x9', 'Ratio has to be 16x9 initially');
  }

  public function testSourceTypeSetAndGet(): void
  {
    $video = new Video();
    $this->assertSame($video->getSourceType(), SourceType::VIMEO, 'Sourcetype has to be Vimeo initially');

    $video->setSourceType(SourceType::YOUTUBE);
    $this->assertSame($video->getSourceType(), SourceType::YOUTUBE, 'Sourcetype has to be Youtube after set to Youtube');
  }
}
