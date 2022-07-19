<?php

namespace Svc\VideoBundle;

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
        ->booleanNode('enableVideoSort')->defaultTrue()->info('Enable video sort combox in video overview?')->end()
        ->scalarNode('thumbnailDir')->defaultValue('%kernel.project_dir%/public/uploads')->cannotBeEmpty()->end()
        ->scalarNode('homeRoute')->defaultValue('svc_video_list')->info('Default route, for redirect after errors')->cannotBeEmpty()->end()
      ->end();
  }

  public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
  {
    $container->import('../config/services.yaml');

    $container->services()
      ->get('Svc\VideoBundle\Controller\VideoController')
      ->arg(0, $config['enableLikes'])
      ->arg(1, $config['enableGroups'])
      ->arg(2, $config['enableShortNames'])
      ->arg(3, $config['enableVideoSort'])
      ->arg(4, $config['homeRoute']);

    $container->services()
      ->get('Svc\VideoBundle\Controller\VideoAdminController')
      ->arg(0, $config['enableShortNames'])
      ->arg(1, $config['enablePrivate'])
      ->arg(2, $config['enableGroups']);

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
  }
}
