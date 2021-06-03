<?php

namespace Svc\VideoBundle\Command;

use Svc\VideoBundle\Service\VideoGroupHelper;
use Svc\VideoBundle\Service\VideoHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SvcVideoManageCommand extends Command
{
  // managed in services.yaml for lazy loading
  //  protected static $defaultName = 'app:svc_video:manage';
  //  protected static $defaultDescription = 'Manage the svc_video bundle';

  private $videoGroupHelper;
  private $videoHelper;

  public function __construct(VideoHelper $videoHelper, VideoGroupHelper $videoGroupHelper)
  {
    parent::__construct();
    $this->videoGroupHelper = $videoGroupHelper;
    $this->videoHelper = $videoHelper;
  }

  protected function configure(): void
  {
    $this
      //            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
      ->addOption('init', null, InputOption::VALUE_NONE, 'Initialize the svc_video bundle (run all parts)')
      ->addOption('createThumbnailDir', null, InputOption::VALUE_NONE, 'Create the thumbnail directory')
      ->addOption('loadThumbnailUrl', null, InputOption::VALUE_NONE, 'Load missing thumbnail urls (or reload all, if --force set)')
      ->addOption('saveThumbnails', null, InputOption::VALUE_NONE, 'Save missing thumbnails (or reload all, if --force set), implicit --loadThumbnailUrl')
      ->addOption('force', null, InputOption::VALUE_NONE, 'Re-create or re-load all files');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);
    $force = $input->getOption('force');
    $stepRun = 0;

    //      $arg1 = $input->getArgument('arg1');

    if ($input->getOption('createThumbnailDir') or $input->getOption('init')) {
      $msg = "";
      $stepRun++;
      if ($this->videoHelper->createThumbnailDir($msg)) {
        $io->info($msg);
      } else {
        $io->error($msg);
        return Command::FAILURE;
      }
    }

    if ($input->getOption('loadThumbnailUrl') or $input->getOption('saveThumbnails') or $input->getOption('init')) {
      $stepRun++;
      $msg = "";
      if ($this->videoHelper->getMissingThumbnailUrl($force, $msg)) {
        $io->info($msg);
      } else {
        $io->error($msg);
        return Command::FAILURE;
      }
    }

    if ($stepRun===0) {
      $io->error("No steps runs. Please check your parameter, you have to set at least one parameter.");
      return Command::FAILURE;
    }

    $io->success("Manage svc_video bundle done. $stepRun steps executed.");
    return Command::SUCCESS;
  }
}
