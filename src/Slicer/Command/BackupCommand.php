<?php

namespace Slicer\Command;

use Slicer\Service;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BackupCommand
 *
 * @package Slicer\Command
 */
class BackupCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName( 'backup' )
            ->setDescription( 'Create a new backup' )
            ->addArgument(
                'base_dir',
                InputArgument::OPTIONAL,
                'The path to the base directory',
                __DIR__ . '/../../'
            );
    }

    public function execute( InputInterface $input, OutputInterface $output )
    {
        //$this->service->backup();
    }
}