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
use Slicer\Event\PreBackupEvent;
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
     * @return bool
     */
    public function backup( array $options )
    {
        $cwd = getcwd();

        chdir( base_path() );

        $options = array_merge_recursive( $options, $this->config->getOptions() );

        $this->event->dispatch( PreBackupEvent::class, new PreBackupEvent( $options ) );

        if ( $options[ 'output' ][ 'debug' ] )
        {
            print_r( $options );
        }

        try
        {
            $backupDir = $options[ 'backup-file' ];

            if ( file_exists( $backupDir ) )
            {
                unlink( $backupDir );
            }

            $finder = new Finder();
            $finder->files()
                ->ignoreVCS( TRUE )
                ->ignoreUnreadableDirs( TRUE )
                ->exclude( $this->config->getBackup()[ 'exclude-dirs' ] )
                ->in( $this->config->getBaseDir() );

            $archive = new ZipArchive();

            $status = $archive->open( $backupDir, ZipArchive::CREATE );

            if ( TRUE === $status )
            {
                foreach ( $finder as $file )
                {
                    $this->addFile( $archive, $file );
                }

                $archive->close();
            }
            else
            {
                return $status;
            }
        }
        catch ( Exception $e )
        {
            return $e;
        }

        chdir( $cwd );

        $this->event->dispatch( PostBackupEvent::class, new PostBackupEvent( $backupDir, $options ) );

        return TRUE;
    }

    /**
     * Restore from backup.
     *
     * @return bool
     */
    public function restore()
    {
        // TODO: Implement restore() method.
    }

    protected function addFile( ZipArchive $archive, SplFileInfo $file )
    {
        $path = strtr( str_replace( $this->config->getBaseDir() . DIRECTORY_SEPARATOR, '', $file->getRealPath() ), '\\', '/' );

        $archive->addFile( $file->getRealPath(), $path );
    }
}