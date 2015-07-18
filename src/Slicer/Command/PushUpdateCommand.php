<?php

namespace Slicer\Command;

use Slicer\Contract\IDownloadManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PushUpdateCommand
 *
 * @package Slicer\Command
 */
class PushUpdateCommand extends Command
{
    /** @var  IDownloadManager */
    protected $downloadManager;

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName( 'push' )
            ->setDescription( 'Push update to server' )
            ->addArgument(
                'version',
                InputArgument::OPTIONAL,
                'Upgrade version',
                NULL
            );
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $this->downloadManager = $this->getApplication()->getSlicer()->getDownloadManager();

    }
}