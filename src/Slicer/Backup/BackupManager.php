<?php

namespace Slicer\Backup;

use Exception;
use Slicer\Contract\IBackupManager;
use Slicer\Manager;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use ZipArchive;

/**
 * Class BackupManager
 *
 * @package Slicer\Backup
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

        print_r( $options );

        try
        {
            if ( file_exists( $options[ 'file' ] ) )
            {
                unlink( $options[ 'file' ] );
            }

            $finder = new Finder();
            $finder->files()
                ->ignoreVCS( TRUE )
                ->ignoreUnreadableDirs( TRUE )
                ->exclude( $this->config->getOptions()[ 'exclude-dirs' ] )
                ->in( $this->config->getBaseDir() );

            $archive = new ZipArchive();
            $file    = $options[ 'file' ];
            $archive->open( $file, ZipArchive::CREATE );

            foreach ( $finder as $file )
            {
                $this->addFile( $archive, $file );
            }

            $archive->close();
        }
        catch ( Exception $e )
        {
            return $e;
        }

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