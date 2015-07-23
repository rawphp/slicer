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

namespace Slicer\Manager\Backup;

use Exception;
use Slicer\Event\PostBackupEvent;
use Slicer\Event\PostRestoreBackupEvent;
use Slicer\Event\PreBackupEvent;
use Slicer\Event\PreRestoreBackupEvent;
use Slicer\Manager\Contract\IBackupManager;
use Slicer\Manager\Manager;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use ZipArchive;

/**
 * Class BackupManager
 *
 * @package Slicer\Manager\Backup
 */
class BackupManager extends Manager implements IBackupManager
{
    /**
     * Create a backup.
     *
     * @param array $options
     *
     * @return bool|Exception
     */
    public function backup( array $options )
    {
        $cwd = getcwd();

        chdir( $options[ 'base-dir' ] );

        $options = array_replace_recursive( $this->config->getOptions(), $options );

        $this->event->dispatch( PreBackupEvent::class, new PreBackupEvent( $options ) );

        if ( $options[ 'output' ][ 'debug' ] )
        {
            print_r( $options );
        }

        $backupFile = $options[ 'backup-file' ];

        try
        {
            if ( file_exists( $backupFile ) )
            {
                unlink( $backupFile );
            }

            $finder = new Finder();
            $finder->files()
                ->ignoreVCS( TRUE )
                ->ignoreUnreadableDirs( TRUE )
                ->exclude( $this->config->getBackup()[ 'exclude-dirs' ] )
                ->in( $options[ 'base-dir' ] );

            $archive = new ZipArchive();

            $status = $archive->open( $backupFile, ZipArchive::CREATE );

            if ( TRUE === $status )
            {
                foreach ( $finder as $file )
                {
                    $this->addFile( $archive, $file );
                }

                $archive->close();

                $result = TRUE;
            }
            else
            {
                $result = $status;
            }
        }
        catch ( Exception $e )
        {
            $result = $e;
        }

        chdir( $cwd );

        $this->event->dispatch( PostBackupEvent::class, new PostBackupEvent( $backupFile, $options ) );

        return $result;
    }

    /**
     * Restore from backup.
     *
     * @param array $options
     *
     * @return bool
     */
    public function restore( array $options )
    {
        $cwd = getcwd();

        chdir( $options[ 'base-dir' ] );

        $options = array_replace_recursive( $this->config->getOptions(), $options );

        if ( $options[ 'output' ][ 'debug' ] )
        {
            print_r( $options );
        }

        $event = new PreRestoreBackupEvent( $options );
        $this->event->dispatch( PreRestoreBackupEvent::class, $event );

        try
        {
            if ( !is_writable( $options[ 'base-dir' ] ) )
            {
                return new Exception( 'Directory: "' . $options[ 'base-dir' ] . '" is not writable' );
            }

            $archive = new ZipArchive();

            $archive->open( $options[ 'file' ] );

            $archive->extractTo( $options[ 'base-dir' ] );

            $archive->close();

            $status = TRUE;
        }
        catch ( Exception $e )
        {
            $status = FALSE;
        }

        $event = new PostRestoreBackupEvent( $status, $options );
        $this->event->dispatch( PostRestoreBackupEvent::class, $event );

        chdir( $cwd );

        return $event->status();
    }

    protected function addFile( ZipArchive $archive, SplFileInfo $file )
    {
        $path = strtr( str_replace( $this->config->getBaseDir() . DIRECTORY_SEPARATOR, '', $file->getRealPath() ), '\\', '/' );

        $archive->addFile( $file->getRealPath(), $path );
    }
}