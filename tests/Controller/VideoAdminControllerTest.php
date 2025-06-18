<?php

/*
 * This file is part of the SvcVideo bundle.
 *
 * (c) Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\VideoBundle\Tests\Controller;

use Doctrine\DBAL\Exception\TableNotFoundException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VideoAdminControllerTest extends KernelTestCase
{
    public function testLogIndex(): void
    {
        $kernel = self::bootKernel();
        $client = new KernelBrowser($kernel);
        $client->followRedirects();

        try {
            $client->request('GET', '/svc-video/admin/');
        } catch (TableNotFoundException $e) {
        }

        $this->assertStringContainsString('General error: 1 no such table: Video', (string) $client->getResponse()->getContent());
    }
}
