<?php

namespace Svc\VideoBundle\Command;

use Svc\VideoBundle\Service\VideoHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SvcVideoManageCommand extends Command
{
  public function __construct(private VideoHelper $videoHelper)
  {
    parent::__construct();
  }

  protected function configure(): void
  {
    $this
      ->addOption('init', 'i', InputOption::VALUE_NONE, 'Initialize the svc_video bundle (run all parts)')
      ->addOption('createThumbnailDir', 'd', InputOption::VALUE_NONE, 'Create the thumbnail directory')
      ->addOption('loadMetadata', 'l', InputOption::VALUE_NONE, 'Load missing metadata (or reload all, if --force set)')
      ->addOption('copyThumbnails', 'c', InputOption::VALUE_NONE, 'Copy missing thumbnails (or reload all, if --force set), implicit --loadMetadata')
      ->addOption('force', 'f', InputOption::VALUE_NONE, 'Re-create or re-load all files');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);
    $force = $input->getOption('force');
    $stepRun = 0;

    if ($input->getOption('createThumbnailDir') or $input->getOption('init')) {
      $msg = '';
      ++$stepRun;
      if ($this->videoHelper->createThumbnailDir($msg)) {
        $io->info($msg);
      } else {
        $io->error($msg);

        return Command::FAILURE;
      }
    }

    if ($input->getOption('loadMetadata') or $input->getOption('copyThumbnails') or $input->getOption('init')) {
      ++$stepRun;
      $msg = '';
      if ($this->videoHelper->getMissingMetadata($force, $msg)) {
        $io->info($msg);
      } else {
        $io->error($msg);

        return Command::FAILURE;
      }
    }

    if ($input->getOption('copyThumbnails') or $input->getOption('init')) {
      ++$stepRun;
      $msg = '';
      if ($this->videoHelper->getMissingThumbnails($force, $msg)) {
        $io->info($msg);
      } else {
        $io->error($msg);

        return Command::FAILURE;
      }
    }

    if ($stepRun === 0) {
      $io->error('No steps runs. Please check your parameter, you have to set at least one parameter.');

      return Command::FAILURE;
    }

    $io->success("Manage svc_video bundle done. $stepRun steps executed.");

    return Command::SUCCESS;
  }
}
