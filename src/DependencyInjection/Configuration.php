<?php

namespace Svc\VideoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
  public function getConfigTreeBuilder()
  {
    $treeBuilder = new TreeBuilder('svc_video'); # ohne Bundle, so muss es dann im yaml-file heissen
    $rootNode = $treeBuilder->getRootNode();
 
    $rootNode
      ->children()
        ->booleanNode('enableLikes')->defaultFalse()->info('Enable likes for videos?')->end()
        ->booleanNode('enableShortNames')->defaultFalse()->info('Enable short names for videos (for short URLs)?')->end()
      ->end();
    return $treeBuilder;

  }

}