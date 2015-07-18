<?php

namespace Slicer\Command;

use Slicer\Contract\IUpdateManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateCommand
 *
 * @package Slicer\Command
 */
class CreateCommand extends Command
{
    /** @var IUpdateManager */
    protected $updateManager;

    protected function configure()
    {
        $this
            ->setName( 'create' )
            ->setDescription( 'Create a new update' )
            ->addArgument(
                'provider',
                InputArgument::REQUIRED,
                'The change file provider to use',
                NULL
            )
            ->addArgument(
                'starting-version',
                InputArgument::REQUIRED,
                'The name of the starting version',
                NULL
            )
            ->addArgument(
                'ending-version',
                InputArgument::REQUIRED,
                'The name of the ending version',
                NULL
            );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $this->updateManager = $this->getApplication()->getSlicer()->getUpdateManager();

        $this->updateManager->createUpdate( $input->getArgument( 'starting-version' ), $input->getArgument( 'ending-version' ) );
    }
}