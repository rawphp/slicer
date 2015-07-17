<?php

namespace Slicer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConfigCommand
 *
 * @package Slicer\Command
 */
class ConfigCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName( 'init' )
            ->setDescription( 'Initialize and generate a default configuration file' )
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'Location for the config file',
                NULL
            );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {

    }
}