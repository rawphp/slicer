<?php

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
        $options = array_merge_recursive( $options, $this->config->getOptions() );

        $this->event->dispatch( PreBackupEvent::class, new PreBackupEvent( $options ) );

        if ( $options[ 'output' ][ 'debug' ] )
        {
            print_r( $options );
        }

        try
        {
            if ( file_exists( $options[ 'backup-file' ] ) )
            {
                unlink( $options[ 'backup-file' ] );
            }

            $finder = new Finder();
            $finder->files()
                ->ignoreVCS( TRUE )
                ->ignoreUnreadableDirs( TRUE )
                ->exclude( $this->config->getOptions()[ 'exclude-dirs' ] )
                ->in( $this->config->getBaseDir() );

            $archive = new ZipArchive();

            $status = $archive->open( $options[ 'backup-file' ], ZipArchive::CREATE );

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

        $this->event->dispatch( PostBackupEvent::class, new PostBackupEvent( $options[ 'backup-file' ], $options ) );

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