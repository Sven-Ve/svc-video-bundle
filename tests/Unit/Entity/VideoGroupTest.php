<?php

declare(strict_types=1);

/*
 * This file is part of the SvcVideo bundle.
 *
 * (c) Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
