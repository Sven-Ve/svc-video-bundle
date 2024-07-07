<?php

namespace Svc\VideoBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Repository\VideoRepository;
use Svc\VideoBundle\Service\VideoHelper;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VideoHelperTest extends TestCase
{
  private VideoHelper $videoHelper;

  protected function setUp(): void
  {
    parent::setUp();

    $mockVideoRep = $this->createMock(VideoRepository::class);
    $mockEntityMan = $this->createMock(EntityManagerInterface::class);
    $mockRequestStack = $this->createMock(RequestStack::class);
    $mockUrlGen = $this->createMock(UrlGeneratorInterface::class);

    $this->videoHelper = new VideoHelper(
      '/tmp',
      true,
      true,
      $mockVideoRep,
      $mockEntityMan,
      $mockRequestStack,
      $mockUrlGen
    );
  }

  public function testServiceCreate(): void
  {
    $this->assertInstanceOf(VideoHelper::class, $this->videoHelper, 'Create instance');
  }

  public function testGetVideoRatios(): void
  {
    $this->assertSame(['1x1', '4x3', '16x9', '21x9'], $this->videoHelper->getRatioList(), 'Get ratio list');
  }

  public function testEncryptAndDecryptPassword(): void
  {
    $video = new Video();
    $this->assertNull($this->videoHelper->getEncPassword($video), 'get encrypted password for empty video class');

    $video->setIsPrivate(true);
    $video->setPlainPassword('Test');
    $encPassword = $this->videoHelper->getEncPassword($video);

    /* @phpstan-ignore argument.type */
    $this->assertTrue($this->videoHelper->checkPassword('Test', $encPassword), 'Check password match');
  }

  /*
  public function testGetVideoMetadata() {


    $video = new Video();
    $video->setSourceType(SourceType::VIMEO);
    $video->setSourceID(556790962);

    $this->assertTrue($this->videoHelper->getVideoMetadata($video));
  }
  */
}
