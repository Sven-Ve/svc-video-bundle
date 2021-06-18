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
        ->booleanNode('enableGroups')->defaultFalse()->info('Enable videos groups?')->end()
        ->booleanNode('enablePrivate')->defaultTrue()->info('Enable private videos?')->end()
        ->scalarNode('thumbnailDir')->defaultValue('%kernel.project_dir%/public/uploads')->cannotBeEmpty()->end()
        ->scalarNode('homeRoute')->defaultValue("svc_video_list")->info('Default route, for redirect after errors')->cannotBeEmpty()->end()
      ->end();
    return $treeBuilder;

  }

}