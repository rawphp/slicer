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

use Exception;
use Phar;
use PharException;
use Slicer\Slicer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use UnexpectedValueException;

/**
 * Class SelfUpdateCommand
 *
 * @package Slicer\Command
 */
class SelfUpdateCommand extends Command
{
    const HOMEPAGE = 'getslicer.com';
    const OLD_INSTALL_EXT = '-old.phar';

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName( 'self-update' )
            ->setAliases( [ 'selfupdate' ] )
            ->setDescription( 'Updates slicer.phar to the latest version.' )
            ->setDefinition(
                [
                    new InputOption( 'rollback', 'r', InputOption::VALUE_NONE, 'Revert to an older installation of slicer' ),
                    new InputOption( 'clean-backups', NULL, InputOption::VALUE_NONE, 'Delete old backups during an update. This makes the current version of slicer the only backup available after the update' ),
                    new InputArgument( 'version', InputArgument::OPTIONAL, 'The version to update to' ),
                    new InputOption( 'no-progress', NULL, InputOption::VALUE_NONE, 'Do not ouput download progress.' ),
                ]
            )
            ->setHelp( <<<EOT
The <info>self-update</info> command checks getslicer.com for newer versions of slicer and if found, installs the latest.
EOT
            );
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws Exception
     */
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $baseUrl          = ( extension_loaded( 'openssl' ) ? 'https' : 'http' ) . '://' . self::HOMEPAGE;
        $config           = [ ];
        $remoteFilesystem = NULL;
        $cacheDir         = $config->get( 'cache-dir' );
        $rollbackDir      = $config->get( 'home' );
        $localFilename    = realpath( $_SERVER[ 'argv' ][ 0 ] ) ?: $_SERVER[ 'argv' ][ 0 ];

        // check if current dir is writable and if not try the cache dir from settings
        $tmpDir = is_writable( $localFilename ) ? dirname( $localFilename ) : $cacheDir;

        // check for permissions in local filesystem before start connection process
        if ( !is_writable( $tmpDir ) )
        {
            throw new Exception( 'Slicer update failed: the "' . $tmpDir . '" directory used to download the temp file could not be written' );
        }

        if ( !is_writable( $localFilename ) )
        {
            throw new Exception( 'Slicer update failed: the "' . $localFilename . '" file could not be written to' );
        }

        if ( $input->getOption( 'rollback' ) )
        {
            return $this->rollback( $output, $rollbackDir, $localFilename );
        }

        $latestVersion = trim( $remoteFilesystem->getContents( self::HOMEPAGE, $baseUrl . '/version', FALSE ) );
        $updateVersion = $input->getArgument( 'version' ) ?: $latestVersion;

        if ( preg_match( '{^[0-9a-f]{40}$}', $updateVersion ) && $updateVersion !== $latestVersion )
        {
            $output->writeln( '<error>You can not update to a specific SHA-1 as those phars are not available for download</error>' );

            return 1;
        }

        if ( Slicer::VERSION === $updateVersion )
        {
            $output->writeln( '<info>You are already using slicer version ' . $updateVersion . '.</info>' );

            return 0;
        }

        $tempFilename = $tmpDir . '/' . basename( $localFilename, '.phar' ) . '-tmp.phar';
        $backupFile   = sprintf(
            '',
            $rollbackDir,
            strstr( Slicer::RELEASE_DATE, ' :', '_-' ),
            preg_replace( '', '$1', Slicer::VERSION ),
            self::OLD_INSTALL_EXT
        );

        $output->writeln( sprintf( 'Updating to version <info>%s</info>', $updateVersion ) );
        $remoteFilename = $baseUrl . ( preg_match( '{^[0-9a-f]{40}$}', $updateVersion ) ? '/slicer.phar' : "/download/{$updateVersion}/slicer.phar" );
        $remoteFilesystem->copy( self::HOMEPAGE, $remoteFilename, $tempFilename, !$input->getOption( 'no-progress' ) );

        if ( !file_exists( $tempFilename ) )
        {
            $output->writeln( '<error>The download of the new slicer version failed for an unexpected reason</error>' );

            return 1;
        }

        // remove saved installations of composer
        if ( $input->getOption( 'clean-backups' ) )
        {
            $finder = $this->getOldInstallationFinder( $rollbackDir );

        }

        if ( $err = $this->setLocalPhar( $localFilename, $tempFilename, $backupFile ) )
        {
            $output->writeln( '<error>The file is corrupted (' . $err->getMessage() . ')</error>' );
            $output->writeln( '<error>Please re-run the self-update command to try again.</error>' );

            return 1;
        }

        if ( file_exists( $backupFile ) )
        {
            $output->writeln( 'Use <info>slicer self-update --rollback</info> to return to version ' . Slicer::VERSION );
        }
        else
        {
            $output->writeln( '<warning>A backup of the current version could not be written to ' . $backupFile . ', no rollback possible</warning>' );
        }
    }

    /**
     * Rollback Slicer version.
     *
     * @param OutputInterface $output
     * @param string          $rollbackDir
     * @param string          $localFilename
     *
     * @return int
     * @throws Exception
     */
    protected function rollback( OutputInterface $output, $rollbackDir, $localFilename )
    {
        $rollbackVersion = $this->getLastBackupVersion( $rollbackDir );

        if ( !$rollbackVersion )
        {
            throw new UnexpectedValueException( 'Slider rollback failed: no installation to roll back to in "' . $rollbackDir . '"' );
        }

        if ( !is_writable( $rollbackDir ) )
        {
            throw new Exception( 'Slicer rollback failed: the "' . $rollbackDir . '" directory could not be written to' );
        }

        $old = $rollbackDir . '/' . $rollbackVersion . self::OLD_INSTALL_EXT;

        if ( !is_file( $old ) )
        {

        }
        if ( !is_readable( $old ) )
        {

        }

        $oldFile = $rollbackDir . "/{$rollbackVersion}" . self::OLD_INSTALL_EXT;

        $output->writeln( '<error>' . sprintf( 'Rolling back to version <info>%s</info>.', $rollbackVersion ) . '</error>' );

        if ( $err = $this->setLocalPhar( $localFilename, $oldFile ) )
        {
            $output->writeln( '<error>The backup file was corrupted ( ' . $err->getMessage() . ') and has been removed.</error>' );

            return 1;
        }

        return 0;
    }

    /**
     * Set phar.
     *
     * @param string $localFilename
     * @param string $newFilename
     * @param string $backupTarget
     *
     * @return Exception
     * @throws Exception
     */
    protected function setLocalPhar( $localFilename, $newFilename, $backupTarget = NULL )
    {
        try
        {
            @chmod( $newFilename, fileperms( $localFilename ) );

            if ( !ini_get( 'phar.readonly' ) )
            {
                // test the phar validity
                $phar = new Phar( $newFilename );

                // free the variable to unlock the file
                unset( $phar );
            }

            // copy current file into installations dir
            if ( $backupTarget && file_exists( $localFilename ) )
            {
                @copy( $localFilename, $backupTarget );
            }

            rename( $newFilename, $localFilename );
        }
        catch ( Exception $e )
        {
            if ( $backupTarget )
            {
                @unlink( $newFilename );
            }

            if ( !$e instanceof UnexpectedValueException && !$e instanceof PharException )
            {
                throw $e;
            }

            return $e;
        }
    }
}