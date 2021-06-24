<?php

namespace Svc\VideoBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SvcVideoExtension extends Extension
{
  public function load(array $configs, ContainerBuilder $container)
  {
    $rootPath = $container->getParameter("kernel.project_dir");
    $this->createConfigIfNotExists($rootPath);

    $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('services.xml');

    $configuration = $this->getConfiguration($configs, $container);
    $config = $this->processConfiguration($configuration, $configs);

    $definition = $container->getDefinition('svc_video.controller');
    $definition->setArgument(0, $config['enableLikes']);
    $definition->setArgument(1, $config['enableGroups']);
    $definition->setArgument(2, $config['enableShortNames']);
    $definition->setArgument(3, $config['homeRoute']);

    $definition = $container->getDefinition('svc_video.controller.admin');
    $definition->setArgument(0, $config['enableShortNames']);
    $definition->setArgument(1, $config['enablePrivate']);

    $definition = $container->getDefinition('svc_video.service.video-helper');
    $definition->setArgument(0, $config['thumbnailDir']);
    $definition->setArgument(1, $config['enablePrivate']);
    $definition->setArgument(2, $config['enableShortNames']);

    $definition = $container->getDefinition('svc_video.controller.group');
    $definition->setArgument(0, $config['enableShortNames']);
    $definition->setArgument(1, $config['enablePrivate']);
  }

  private function createConfigIfNotExists($rootPath)
  {
    $fileName = $rootPath . "/config/routes/svc_video.yaml";
    if (!file_exists($fileName)) {
      $text = "_svc_video:\n";
      $text .= "    resource: '@SvcVideoBundle/src/Resources/config/routes.xml'\n";
      $text .= "    prefix: /svc-video/{_locale}\n";
      $text .= '    requirements: {"_locale": "%app.supported_locales%"}}\n';
      try {
        file_put_contents($fileName, $text);
        dump("Please adapt config file $fileName");
      } catch (Exception $e) {
        // ignore...
      }
    }

    $fileName = $rootPath . "/config/packages/svc_video.yaml";
    if (!file_exists($fileName)) {
      $text = "svc_video:\n";
      $text .= "    # Enable likes for videos\n";
      $text .= "    enableLikes: false\n";
      $text .= "    # Enable short names for videos (for short URLs)?\n";
      $text .= "    enableShortNames: false\n";
      $text .= "    # Enable videos groups?\n";
      $text .= "    enableGroups: false\n";
      $text .= "    # Default route, for redirect after errors (default = VideoOverview svc_video_list)\n";
      $text .= "    homeRoute: svc_video_list\n";
      try {
        file_put_contents($fileName, $text);
        dump("Please adapt config file $fileName");
      } catch (Exception $e) {
        // ignore...
      }
    }
  }
}
