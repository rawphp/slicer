<?php

namespace Slicer\Command;

use Slicer\Contract\IUpdateManager;
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
    /** @var  IUpdateManager */
    protected $updateManager;

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
        $this->updateManager = $this->getApplication()->getSlicer()->getUpdateManager();

        $this->updateManager->update( NULL );
    }
}