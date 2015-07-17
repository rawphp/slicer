<?php

namespace Slicer\Command;

use Slicer\Service;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateCommand
 *
 * @package RawPHP\Slicer\Command
 */
class UpdateCommand extends Command
{
    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName( 'update' )
            ->setDescription( 'Run update' )
            ->addArgument(
                'version',
                InputArgument::OPTIONAL,
                'Upgrade version',
                NULL
            );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {

    }
}