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

namespace Slicer\Manager\Update;

use DateTime;
use Exception;
use Phar;
use Seld\PharUtils\Timestamps;
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
use Slicer\Update\Generator\ClassGenerator;
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
            $this->compileUpdatePhar( $updateZipFile, $className . '.php', $filesZip );
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

                $zip->addFile( $file, str_replace( $this->config->getBaseDir(), '', $info->getPathname() ) );
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

        $snippetsDir = $this->config->getBaseDir() . '/res/';

        $contents = file_get_contents( $snippetsDir . 'header.txt' );

        $changedFiles[ 'deleted' ][] = 'file.txt';

        $contents .= ClassGenerator::generateUpdateFilesMethod( $className, $this->config->getBaseDir(), array_merge( $changedFiles[ 'added' ], $changedFiles[ 'modified' ] ) );
        $contents .= PHP_EOL;
        $contents .= ClassGenerator::generateDeleteFilesMethod( $changedFiles[ 'deleted' ] );
        $contents .= PHP_EOL;
        $contents .= ClassGenerator::generateUpdateDatabaseMethod();
        $contents .= PHP_EOL;
        $contents .= ClassGenerator::generateRollbackUpdateFilesMethod();
        $contents .= PHP_EOL;
        $contents .= ClassGenerator::generateRollbackDeleteFilesMethod();
        $contents .= PHP_EOL;
        $contents .= ClassGenerator::generateRollbackDatabaseChangesMethod();

        $contents .= file_get_contents( $snippetsDir . 'footer.txt' );

        $contents = str_replace( '{namespace}', $this->config->getUpdateNamespace(), $contents );
        $contents = str_replace( '{class_name}', $className, $contents );

        return ( 0 < file_put_contents( $file, $contents ) );
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

//    public function createFinalUpdateZip( $updateZipFile, $classFile, $filesZip )
//    {
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
//    }

    /**
     * Create an executable update phar.
     *
     * @param SplFileInfo $classFile
     * @param SplFileInfo $filesZip
     *
     * @return bool
     */
    public function compileUpdatePhar( SplFileInfo $classFile, SplFileInfo $filesZip )
    {
        try
        {
            $pharFile = base_path( 'Update.phar' );

            if ( file_exists( $pharFile ) )
            {
                unlink( $pharFile );
            }

            $phar = new Phar( $pharFile, 0, 'Update.phar' );
            $phar->setSignatureAlgorithm( Phar::SHA1 );

            $phar->startBuffering();

            // Add Update Class
            $path    = strtr( str_replace( base_path() . DIRECTORY_SEPARATOR, '', $classFile->getRealPath() ), '\\', '/' );
            $content = file_get_contents( $path );
            $phar->addFromString( $path, $content );

            // Add Zip File
            $path    = 'res/files.zip';
            $content = file_get_contents( $filesZip );
            $phar->addFromString( $path, $content );

            // Add update bin script
            $this->addUpdateBin( $phar );

            // Generate a Stub
            $stub = $this->generateUpdateStub( $pharFile );
            $phar->setStub( $stub );

            $phar->stopBuffering();

            // re-sign the phar with reproducible timestamp / signature
            $util = new Timestamps( $pharFile );
            $util->updateTimestamps( ( new DateTime() )->format( 'U' ) );
            $util->save( $pharFile, Phar::SHA1 );

            return TRUE;
        }
        catch ( Exception $e )
        {
            echo $e->getMessage();
        }

        return FALSE;
    }

    /**
     * Add Update Center Bin contents.
     *
     * @param Phar $phar
     */
    private function addUpdateBin( Phar $phar )
    {
        $content = file_get_contents( base_path( 'res/update' ) );
        $content = preg_replace( '{^#!/usr/bin/env php\s*}', '', $content );

        $phar->addFromString( 'bin/update', $content );
    }

    /**
     * Generate an update Phar stub.
     *
     * @param string $pharFileName
     *
     * @return string
     */
    public function generateUpdateStub( $pharFileName )
    {
        $path = strtr( str_replace( base_path() . DIRECTORY_SEPARATOR, '', $pharFileName ), '\\', '/' );

        $stub = "
#!/usr/bin/env php
<?php

/*
 * This file was generated by Slicer.
 *
 * (c) Tom Kaczocha <tom@rawphp.org>
 *
 * For the full copyright and license information, please view
 * the license that is located at the bottom of this file.
 */

Phar::mapPhar( '{$path}' );

";

        return $stub . "
require 'phar://{$path}/bin/update';

__HALT_COMPILER();
";
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