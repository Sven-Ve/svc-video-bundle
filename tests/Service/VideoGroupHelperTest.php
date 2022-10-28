<?php

namespace Svc\VideoBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Svc\VideoBundle\Entity\VideoGroup;
use Svc\VideoBundle\Exception\DefaultVideoGroupNotExistsException;
use Svc\VideoBundle\Repository\VideoGroupRepository;
use Svc\VideoBundle\Service\VideoGroupHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VideoGroupHelperTest extends TestCase
{
  private VideoGroupHelper $videoGroupHelper;

  private VideoGroupRepository $mockVideoGroupRep;

  private EntityManagerInterface $mockEntityMan;

  protected function setUp(): void
  {
    parent::setUp();

    $this->mockVideoGroupRep = $this->createMock(VideoGroupRepository::class);
    $this->mockEntityMan = $this->createMock(EntityManagerInterface::class);
    $mockUrlGen = $this->createMock(UrlGeneratorInterface::class);

    $this->videoGroupHelper = new VideoGroupHelper(
      true,
      $this->mockVideoGroupRep,
      $this->mockEntityMan,
      $mockUrlGen
    );
  }

  public function testServiceCreate(): void
  {
    $this->assertInstanceOf(VideoGroupHelper::class, $this->videoGroupHelper, 'Create instance');
  }

  public function testInitDefaultVideoGroupWithoutExistingDefault()
  {
    $this->mockEntityMan
      ->expects(self::once())
      ->method('flush');
    $this->mockEntityMan
      ->expects(self::once())
      ->method('persist');

    $this->mockVideoGroupRep
      ->expects(self::once())
      ->method('findOneBy');

    $this->videoGroupHelper->initDefaultVideoGroup();
  }

  public function testInitDefaultVideoGroupWithExistingDefault()
  {
    $this->mockEntityMan
      ->expects(self::never())
      ->method('flush');
    $this->mockEntityMan
      ->expects(self::never())
      ->method('persist');

    $videoGroup = new VideoGroup();
    $videoGroup->setDefaultGroup(true);

    $this->mockVideoGroupRep
      ->expects(self::once())
      ->method('findOneBy')
      ->willReturn($videoGroup);

    $this->videoGroupHelper->initDefaultVideoGroup();
  }

  public function testGetDefaultVideoGroupWithExistingDefault()
  {
    $videoGroup = new VideoGroup();
    $videoGroup->setDefaultGroup(true);
    $videoGroup->setName('Default Group');

    $this->mockVideoGroupRep
      ->expects(self::once())
      ->method('findOneBy')
      ->willReturn($videoGroup);

    $defaultVGroup = $this->videoGroupHelper->getDefaultVideoGroup();
    $this->assertSame('Default Group', $defaultVGroup->getName(), 'Get Default Video Group name');
  }

  public function testGetDefaultVideoGroupWithoutExistingDefault()
  {
    $this->mockVideoGroupRep
      ->expects(self::once())
      ->method('findOneBy')
      ->willReturn(null);

    $this->expectExceptionMessage('Default video group not found. Please initialize the app.');
    $this->expectExceptionObject(new DefaultVideoGroupNotExistsException());
    $this->videoGroupHelper->getDefaultVideoGroup();
  }

  public function testGetVideoGroupsAll()
  {
    $videoGroup = new VideoGroup();
    $videoGroup->setName("Test");

    $this->mockVideoGroupRep
      ->expects(self::once())
      ->method('findAll')
      ->willReturn([$videoGroup]);

    $this->assertSame([$videoGroup], $this->videoGroupHelper->getVideoGroups(false));
  }

  public function testGetVideoGroupsOnlyVisiblesOnHomePage()
  {
    $videoGroup = new VideoGroup();
    $videoGroup->setName("Test");

    $this->mockVideoGroupRep
      ->expects(self::once())
      ->method('findAllExceptHidenOnHomePage')
      ->willReturn([$videoGroup]);

    $this->assertSame([$videoGroup], $this->videoGroupHelper->getVideoGroups(true));
  }
}
