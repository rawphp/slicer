<?php

namespace Slicer\Command;

use Exception;
use Slicer\Contract\IBackupManager;
use Slicer\Service;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BackupCommand
 *
 * @package Slicer\Command
 */
class BackupCommand extends Command
{
    /** @var  IBackupManager */
    protected $backupManager;

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName( 'backup' )
            ->setDescription( 'Create a new backup' )
            ->addArgument(
                'base_dir',
                InputArgument::OPTIONAL,
                'The path to the base directory'
            )
            ->addOption( 'full', 'f', InputOption::VALUE_NONE, 'Create a full backup' )
            ->addOption( 'restore', 'r', InputOption::VALUE_NONE, 'Only create a restore backup [default]' );
    }

    /**
     * Execute backup command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute( InputInterface $input, OutputInterface $output )
    {
        $this->backupManager = $this->getApplication()->getSlicer()->getBackupManager();

        $status = $this->backupManager->backup(
            [
                'file'   => $this->backupManager->getConfig()->getCacheDir() . '/backup.zip',
                'type'   => $input->hasOption( 'full' ) ? 'full' : 'restore',
                'output' =>
                    [
                        'debug'   => $input->hasOption( 'debug' ) ? $input->getOption( 'debug' ) : FALSE,
                        'quiet'   => $input->hasOption( 'quiet' ),
                        'verbose' => $input->hasOption( 'verbose' ) ? $input->getOption( 'verbose' ) : NULL,
                    ]
            ]
        );

        if ( $status instanceof Exception )
        {
            $output->writeln( '<error>' . $status->getMessage() . '</error>' );
        }

        $output->writeln( 'Backup finished: ' . $status );
    }
}