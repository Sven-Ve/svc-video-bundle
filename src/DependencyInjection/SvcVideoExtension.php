<?php

namespace Svc\VideoBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SvcVideoExtension extends Extension
{
  private $rootPath;

  public function load(array $configs, ContainerBuilder $container)
  {
    $this->rootPath = $container->getParameter("kernel.project_dir");
    $this->createConfigIfNotExists();

    $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../../Resources/config'));
    $loader->load('services.xml');

    $configuration = $this->getConfiguration($configs, $container);
    $config = $this->processConfiguration($configuration, $configs);

    $definition = $container->getDefinition('svc_video.controller');
    $definition->setArgument(0, $config['enableLikes']);
    $definition->setArgument(1, $config['enableGroups']);
    $definition->setArgument(2, $config['enableShortNames']);
    $definition->setArgument(3, $config['enableVideoSort']);
    $definition->setArgument(4, $config['homeRoute']);

    $definition = $container->getDefinition('svc_video.controller.admin');
    $definition->setArgument(0, $config['enableShortNames']);
    $definition->setArgument(1, $config['enablePrivate']);
    $definition->setArgument(2, $config['enableGroups']);

    $definition = $container->getDefinition('svc_video.service.video-helper');
    $definition->setArgument(0, $config['thumbnailDir']);
    $definition->setArgument(1, $config['enablePrivate']);
    $definition->setArgument(2, $config['enableShortNames']);

    $definition = $container->getDefinition('svc_video.controller.group');
    $definition->setArgument(0, $config['enableShortNames']);
    $definition->setArgument(1, $config['enablePrivate']);

    $definition = $container->getDefinition('svc_video.service.video-group-helper');
    $definition->setArgument(0, $config['enableShortNames']);
  }

  private function createConfigIfNotExists()
  {
    $fileName = $this->rootPath . "/config/routes/svc_video.yaml";
    if (!file_exists($fileName)) {
      $text = "_svc_video:\n";
      $text .= "    resource: '@SvcVideoBundle/Resources/config/routes.xml'\n";
      $text .= "    prefix: /svc-video/{_locale}\n";
      $text .= '    requirements: {"_locale": "%app.supported_locales%"}}\n';
      try {
        file_put_contents($fileName, $text);
        dump("Please adapt config file $fileName");
      } catch (Exception $e) {
        // ignore...
      }
    }

    $fileName = $this->rootPath . "/config/packages/svc_video.yaml";
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
    $this->createAssetFiles("assets/controllers/svcv-clipboard_controller.js");
    $this->createAssetFiles("assets/controllers/svcv-clipboard-multi_controller.js");
  }

  /**
   * create config and asset files
   *
   * @param string $file
   * @return boolean
   */
  private function createAssetFiles(string $file): bool
  {
    $destFile = $this->rootPath . "/" . $file;
    if (file_exists($destFile)) {
      return true;
    }
    $soureFile =  $this->rootPath . "/vendor/svc/video-bundle/install/" . $file;
    if (!file_exists($soureFile)) {
      dump("Cannot create file " . $file . " (source not exists)");
      return false;
    }

    try {
      copy($soureFile, $destFile);
    } catch (Exception $e) {
      dump("Cannot create file " . $file . " (" . $e->getMessage() . ")");
      return false;
    }
    return true;
  }
}
