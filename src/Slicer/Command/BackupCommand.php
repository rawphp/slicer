<?php

/**
 * This file is part of Slicer.
 *
 * Copyright (c) 2015 Tom Kaczocha <tom@rawphp.org>
 *
 * This Source Code is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, you can obtain one at http://mozilla.org/MPL/2.0/.
 *
 * PHP version 5.6
 */

namespace Slicer\Command;

use DateTime;
use Exception;
use Slicer\Manager\Contract\IBackupManager;
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
            ->addOption( 'type', 't', InputOption::VALUE_OPTIONAL, 'Backup file type [ single, daily, unique ] default( single )' );
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

        $config = $this->backupManager->getConfig();

        $date = new DateTime();

        $dir = base_path( $config->getStorage()[ 'backup-dir' ] );

        $type = $config->getBackup()[ 'file-type' ];

        if ( $input->hasOption( 'type' ) && '' !== trim( $input->getOption( 'type' ) ) )
        {
            $type = $input->getOption( 'type' );

            if ( !in_array( $type, [ 'daily', 'unique', 'single' ] ) )
            {
                $output->writeln( '<error>Unknown type ' . $type . '. Using \'single\' by default.</error>' );
            }
        }

        switch ( $type )
        {
            case 'daily':
                $fileName = clean_slicer_path( $dir . '/backup-' . $date->format( 'Ymd' ) . '.zip' );
                break;
            case 'unique':
                $fileName = clean_slicer_path( $dir . '/backup-' . $date->format( 'Ymdhms' ) . '.zip' );
                break;
            case 'single':
            default:
                $fileName = clean_slicer_path( $dir . '/backup.zip' );
                break;
        }

        $baseDir = base_path();

        if ( $input->hasOption( 'working-dir' ) && '' !== trim( $input->getOption( 'working-dir' ) ) )
        {
            $baseDir = $input->getOption( 'working-dir' );

            if ( !file_exists( $baseDir ) )
            {
                if ( file_exists( base_path( $baseDir ) ) )
                {
                    $baseDir = base_path( $baseDir );
                }
                else
                {
                    $output->writeln( 'Working Directory "' . $baseDir . '" not found' );

                    return 1;
                }
            }
        }

        $status = $this->backupManager->backup(
            [
                'backup-file' => $fileName,
                'backup-type' => $input->hasOption( 'full' ) ? 'full' : 'restore',
                'base-dir'    => $baseDir,
                'output'      =>
                    [
                        'debug'   => $input->getOption( 'debug' ),
                        'quiet'   => ( TRUE === $input->getOption( 'debug' ) ) ? FALSE : TRUE,
                        'verbose' => $input->getOption( 'verbose' ),
                    ]
            ]
        );

        if ( $status instanceof Exception )
        {
            $output->writeln( '<error>' . $status->getMessage() . '</error>' );

            $status = 0;
        }

        $status = ( 1 === ( int ) $status ) ? 'Success' : 'Failed';

        $output->writeln( 'Backup Status: <info>' . $status . '</info>' );
    }
}