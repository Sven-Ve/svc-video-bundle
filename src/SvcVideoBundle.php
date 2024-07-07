<?php

namespace Svc\VideoBundle;

use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SvcVideoBundle extends AbstractBundle
{
  public function getPath(): string
  {
    return \dirname(__DIR__);
  }

  public function configure(DefinitionConfigurator $definition): void
  {
    $definition->rootNode()
      ->children()
        ->booleanNode('enableLikes')->defaultFalse()->info('Enable likes for videos?')->end()
        ->booleanNode('enableShortNames')->defaultFalse()->info('Enable short names for videos (for short URLs)?')->end()
        ->booleanNode('enableGroups')->defaultFalse()->info('Enable videos groups?')->end()
        ->booleanNode('enablePrivate')->defaultTrue()->info('Enable private videos?')->end()
        ->booleanNode('enableVideoSort')->defaultTrue()->info('Enable video sort combobox in video overview?')->end()
        ->booleanNode('enablePagination')->defaultFalse()->info('Enable pagination in video admin (needs babdev/pagerfanta-bundle)?')->end()
        ->booleanNode('enableTagging')->defaultFalse()->info('Enable tagging for videos (needs js library @jcubic/tagger)')->end()
        ->scalarNode('thumbnailDir')->defaultValue('%kernel.project_dir%/public/uploads')->cannotBeEmpty()->end()
        ->scalarNode('homeRoute')->defaultValue('svc_video_list')->info('Default route, for redirect after errors')->cannotBeEmpty()->end()
      ->end();
  }

  /**
   * @param array<mixed> $config
   */
  public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
  {
    $container->import('../config/services.yaml');

    $container->services()
      ->get('Svc\VideoBundle\Controller\VideoController')
      ->arg(0, $config['enableLikes'])
      ->arg(1, $config['enableGroups'])
      ->arg(2, $config['enableShortNames'])
      ->arg(3, $config['enableVideoSort'])
      ->arg(4, $config['homeRoute'])
      ->arg(5, $config['enableTagging']);

    $container->services()
      ->get('Svc\VideoBundle\Controller\VideoAdminController')
      ->arg(0, $config['enableShortNames'])
      ->arg(1, $config['enablePrivate'])
      ->arg(2, $config['enableGroups'])
      ->arg(3, $config['enablePagination'])
      ->arg(4, $config['enableTagging']);

    $container->services()
      ->get('Svc\VideoBundle\Service\VideoHelper')
      ->arg(0, $config['thumbnailDir'])
      ->arg(1, $config['enablePrivate'])
      ->arg(2, $config['enableShortNames']);

    $container->services()
      ->get('Svc\VideoBundle\Controller\VideoGroupController')
      ->arg(0, $config['enableShortNames'])
      ->arg(1, $config['enablePrivate']);

    $container->services()
      ->get('Svc\VideoBundle\Service\VideoGroupHelper')
      ->arg(0, $config['enableShortNames']);

    $container->services()
      ->get('Svc\VideoBundle\Controller\TagAdminController')
      ->arg(0, $config['enableTagging']);
  }

  public function prependExtension(ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
  {
    if (!$this->isAssetMapperAvailable($containerBuilder)) {
      return;
    }

    $containerBuilder->prependExtensionConfig('framework', [
      'asset_mapper' => [
        'paths' => [
          __DIR__ . '/../assets/src' => 'svc/video-bundle/src',
          __DIR__ . '/../assets/styles' => 'svc/video-bundle/styles',
        ],
      ],
    ]);
  }

  private function isAssetMapperAvailable(ContainerBuilder $container): bool
  {
    if (!interface_exists(AssetMapperInterface::class)) {
      return false;
    }

    // check that FrameworkBundle 6.3 or higher is installed
    $bundlesMetadata = $container->getParameter('kernel.bundles_metadata');
    if (!isset($bundlesMetadata['FrameworkBundle'])) {
      return false;
    }

    return is_file($bundlesMetadata['FrameworkBundle']['path'] . '/Resources/config/asset_mapper.php');
  }
}
