<?php

namespace Svc\VideoBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Jbtronics\SettingsBundle\JbtronicsSettingsBundle;
use Svc\LogBundle\SvcLogBundle;
use Svc\UtilBundle\SvcUtilBundle;
use Svc\VideoBundle\SvcVideoBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * Test kernel.
 */
class SvcVideoTestingKernel extends Kernel
{
  use MicroKernelTrait;

  public function registerBundles(): iterable
  {
    yield new FrameworkBundle();
    yield new TwigBundle();
    yield new SvcVideoBundle();
    yield new DoctrineBundle();
    yield new SvcLogBundle();
    yield new SecurityBundle();
    yield new JbtronicsSettingsBundle();
    yield new SvcUtilBundle();
  }

  protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
  {
    $config = [
      'http_method_override' => false,
      'secret' => 'foo-secret',
      'test' => true,
    ];

    $container->loadFromExtension('framework', $config);

    $container->loadFromExtension('doctrine', [
      'dbal' => [
        //          'override_url' => true,
        'driver' => 'pdo_sqlite',
        'url' => 'sqlite:///' . $this->getCacheDir() . '/app.db',
      ],
      'orm' => [
        'auto_generate_proxy_classes' => true,
        'auto_mapping' => true,
        'enable_lazy_ghost_objects' => true,
        'report_fields_where_declared' => true,
      ],
    ]);

    $container->loadFromExtension('security', [
      'providers' => [
        'app_user_provider' => [
          'entity' => [
            'class' => 'App\Entity\User',
          ],
        ],
      ],
      'firewalls' => [
        'main' => [
          'provider' => 'app_user_provider',
        ],
      ],
    ]);
    /*
        $container->loadFromExtension('svc_log', [
          'need_admin_for_view' => false,
        ]); */
  }

  /**
   * load bundle routes.
   *
   * @return void
   */
  private function configureRoutes(RoutingConfigurator $routes)
  {
    $routes->import(__DIR__ . '/../config/routes.yaml')->prefix('/svc-video');
  }
}
