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
      ->end();
    return $treeBuilder;

  }

}