<?php

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
