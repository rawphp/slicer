<?php

namespace Slicer\Manager\Update;

use DateTime;
use Exception;
use Slicer\Contract\IUpdate;
use Slicer\Event\OnGetChangeProviderEvent;
use Slicer\Event\PostCreateUpdateEvent;
use Slicer\Event\PostGetUpdatesEvent;
use Slicer\Event\PostPublishUpdateEvent;
use Slicer\Event\PostRollbackEvent;
use Slicer\Event\PostUpdateEvent;
use Slicer\Event\PreCreateUpdateEvent;
use Slicer\Event\PreGetUpdatesEvent;
use Slicer\Event\PrePublishUpdateEvent;
use Slicer\Event\PreRollbackEvent;
use Slicer\Event\PreUpdateEvent;
use Slicer\Manager\Contract\IDownloadManager;
use Slicer\Manager\Contract\IUpdateManager;
use Slicer\Manager\Manager;
use Slicer\Provider\Contract\IChangeProvider;
use SplFileInfo;
use ZipArchive;

/**
 * Class UpdateManager
 *
 * @package Slicer\Manager\Update
 */
class UpdateManager extends Manager implements IUpdateManager
{
    /** @var  IChangeProvider */
    protected $changeProvider;
    /** @var  IDownloadManager */
    protected $downloadManager;
    /** @var  string */
    protected $tmpDir;
    /** @var  array */
    protected $cleanupFiles;

    /**
     * Create the update.
     *
     * @param string $start
     * @param string $end
     *
     * @return IUpdate
     */
    public function createUpdate( $start, $end )
    {
        $this->event->dispatch( PreCreateUpdateEvent::class, new PreCreateUpdateEvent( $start, $end ) );

        $date = ( new DateTime() );

        $name = 'Update-' . $date->format( 'y-m-d-h-m-s' );

        $this->tmpDir = str_replace( '/', DIRECTORY_SEPARATOR, $this->config[ 'storage' ][ 'source' ][ 'tmp-dir' ] . DIRECTORY_SEPARATOR );

        $className     = 'TestUpdate';// . $date->format( 'ymdhms' ) . '.php';
        $updateZipFile = $name . '.zip';
        $filesZip      = $this->tmpDir . 'files.zip';

        $phar = NULL;

        try
        {
            // get changed files
            $files = $this->changeProvider->getChangedFiles( $this->config->getBaseDir(), $start, $end );

            if ( FALSE === $this->zipChangedFiles( $filesZip, $files ) )
            {
                throw new Exception( 'Failed to zip up changed files' );
            }

            // create update class
            if ( FALSE === $this->createUpdateClass( $className, $this->tmpDir, $files ) )
            {
                throw new Exception( 'Failed to create new update file' );
            }

            // create update zip
            //$this->createFinalUpdateZip( $updateZipFile, $this->tmpDir . $className . '.php', $filesZip );
            $this->createUpdatePhar( $updateZipFile, $className . '.php', $filesZip );
        }
        catch ( Exception $e )
        {
            echo $e->getMessage();
        }

        $this->event->dispatch( PostCreateUpdateEvent::class, new PostCreateUpdateEvent( $phar ) );

        return $phar;
    }

    /**
     * Zip up changed files.
     *
     * @param string $filename
     * @param array  $files changed files
     *
     * @return bool
     */
    public function zipChangedFiles( $filename, array $files )
    {
        // zip changed files
        $zip = new ZipArchive();

        if ( TRUE === $zip->open( $filename, ZipArchive::CREATE ) )
        {
            foreach ( array_merge( $files[ 'added' ], $files[ 'modified' ] ) as $file )
            {
                $info = new SplFileInfo( $file );

                $zip->addFile( $file, str_replace( $this->config[ 'base_dir' ], '', $info->getPathname() ) );
            }

            $zip->close();

            $this->cleanupFiles[] = $filename;

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Create the update file.
     *
     * @param string $className
     * @param string $path
     * @param array  $changedFiles
     *
     * @return bool
     */
    public function createUpdateClass( $className, $path, array $changedFiles )
    {
        $this->cleanupFiles[] = $file = $path . $className . '.php';

        $snippetsDir = $this->config[ 'base_dir' ] . '/snippets/';

        $contents = file_get_contents( $snippetsDir . 'header.txt' );

        $changedFiles[ 'deleted' ][] = 'file.txt';

        $contents .= ClassGenerator::generateUpdateFilesMethod( $className, $this->config[ 'base_dir' ], array_merge( $changedFiles[ 'added' ], $changedFiles[ 'modified' ] ) );
        $contents .= PHP_EOL;
        $contents .= ClassGenerator::generateDeleteFilesMethod( $changedFiles[ 'deleted' ] );

        $contents .= file_get_contents( $snippetsDir . 'footer.txt' );

        $contents = str_replace( '{namespace}', $this->config[ 'update-file' ][ 'namespace' ], $contents );
        $contents = str_replace( '{class_name}', $className, $contents );

        return file_put_contents( $file, $contents );
    }

    /**
     * Cleanup files.
     */
    public function cleanUp()
    {
        foreach ( $this->cleanupFiles as $file )
        {
            if ( file_exists( $file ) )
            {
                unlink( $file );
            }
        }
    }

    /**
     * Create final update zip.
     *
     * @param string $updateZipFile
     * @param string $classFile
     * @param string $filesZip
     *
     * @return bool
     */
    public function createFinalUpdateZip( $updateZipFile, $classFile, $filesZip )
    {
//        $zip = new ZipArchive();
//
//        if ( TRUE === $zip->open( $this->tmpDir . $updateZipFile, ZipArchive::CREATE ) )
//        {
//            $info = new SplFileInfo( $classFile );
//            $zip->addFile( $classFile, str_replace( $this->tmpDir, '', $info->getPathname() ) );
//
//            $info = new SplFileInfo( $filesZip );
//            $zip->addFile( $filesZip, str_replace( $this->tmpDir, '', $info->getPathname() ) );
//
//            $zip->close();
//
//            copy( $this->tmpDir . $updateZipFile, $this->config[ 'storage' ][ 'destination' ][ 'update-dir' ] . DIRECTORY_SEPARATOR . $updateZipFile );
//
//            $this->cleanupFiles[] = $this->tmpDir . $updateZipFile;
//
//            $this->cleanUp();
//
//            return TRUE;
//        }
//
//        return FALSE;
    }

    /**
     * Create an executable update phar.
     *
     * @param string $updatedZipFile
     * @param string $classFile
     * @param string $filesZip
     */
    public function createUpdatePhar( $updatedZipFile, $classFile, $filesZip )
    {

    }

    /**
     * Upload an update to the server.
     *
     * @return mixed
     */
    public function publishUpdate()
    {
        $this->event->dispatch( PrePublishUpdateEvent::class, new PrePublishUpdateEvent() );

        try
        {

        }
        catch ( Exception $e )
        {
            echo $e->getMessage();
        }

        $this->event->dispatch( PostPublishUpdateEvent::class, new PostPublishUpdateEvent() );
    }

    /**
     * Get available updates from the server.
     *
     * @return mixed
     */
    public function getUpdates()
    {
        try
        {

        }
        catch ( Exception $e )
        {

        }
    }

    /**
     * Run an update.
     *
     * @param IUpdate $update
     *
     * @return bool
     */
    public function update( IUpdate $update )
    {
        $this->event->dispatch( PreUpdateEvent::class, new PreUpdateEvent() );

        if ( NULL === $update ) return FALSE;

        try
        {

        }
        catch ( Exception $e )
        {

        }

        $this->event->dispatch( PostUpdateEvent::class, new PostUpdateEvent() );
    }

    /**
     * Rollback an update.
     *
     * @param IUpdate $update
     *
     * @return bool
     */
    public function rollback( IUpdate $update )
    {
        $this->event->dispatch( PreRollbackEvent::class, new PreRollbackEvent() );

        if ( NULL === $update ) return;

        try
        {

        }
        catch ( Exception $e )
        {

        }

        $this->event->dispatch( PostRollbackEvent::class, new PostRollbackEvent() );
    }

    /**
     * Run an update check.
     *
     * @return bool
     */
    public function updateCheck()
    {
        $this->event->dispatch( PreGetUpdatesEvent::class, new PreGetUpdatesEvent() );

        try
        {

        }
        catch ( Exception $e )
        {

        }

        $this->event->dispatch( PostGetUpdatesEvent::class, new PostGetUpdatesEvent() );
    }

    /**
     * Get list of applied updates.
     *
     * @return IUpdate[]
     */
    public function getUpdateHistory()
    {

    }

    /**
     * Get the change provider.
     *
     * @return IChangeProvider
     */
    public function getChangeProvider()
    {
        $event = new OnGetChangeProviderEvent( $this->changeProvider );

        $this->event->dispatch( OnGetChangeProviderEvent::class, $event );

        $this->changeProvider = $event->getChangeProvider();

        return $this->changeProvider;
    }

    /**
     * Set the change provider.
     *
     * @param IChangeProvider $provider
     *
     * @return IUpdateManager
     */
    public function setChangeProvider( IChangeProvider $provider )
    {
        $this->changeProvider = $provider;

        return $this;
    }

    /**
     * @return IDownloadManager
     */
    public function getDownloadManager()
    {
        return $this->downloadManager;
    }

    /**
     * Set download manager.
     *
     * @param IDownloadManager $downloadManager
     *
     * @return IUpdateManager
     */
    public function setDownloadManager( IDownloadManager $downloadManager )
    {
        $this->downloadManager = $downloadManager;

        return $this;
    }
}