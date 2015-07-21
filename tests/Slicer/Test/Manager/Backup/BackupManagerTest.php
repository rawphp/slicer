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

namespace Slicer\Test\Manager\Backup;

use Slicer\Manager\Backup\BackupManager;
use Slicer\Event\PostBackupEvent;
use Slicer\Event\PreBackupEvent;
use Slicer\Manager\Contract\IBackupManager;
use Slicer\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

/**
 * Class BackupManagerTest
 *
 * @package Slicer\Test\Manager\Backup
 */
class BackupManagerTest extends TestCase
{
    /** @var  IBackupManager */
    protected $manager;
    /** @var  bool */
    protected $preVerified = FALSE;
    /** @var  bool */
    protected $postVerified = FALSE;

    /** @var  string */
    protected static $backupFile;
    /** @var  array */
    protected static $backupOptions = [ ];

    /**
     * Setup before each test.
     */
    public function setUp()
    {
        parent::setUp();

        self::$backupFile = $this->tmpDir . 'test-backup.zip';

        self::$backupOptions =
            [
                'backup-file' => self::$backupFile,
                'backup-type' => 'full',
                'output'      =>
                    [
                        'debug' => FALSE,
                    ]
            ];

        $this->manager = new BackupManager( $this->getConfig() );
        $this->manager->setEventDispatcher( new EventDispatcher() );

        $this->preVerified  = FALSE;
        $this->postVerified = FALSE;

        if ( file_exists( $this->tmpDir . 'extracted' ) )
        {
            ( new Filesystem() )->remove( [ $this->tmpDir . 'extracted' ] );
        }
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();

        if ( file_exists( self::$backupFile ) )
        {
            unlink( self::$backupFile );
        }

        if ( file_exists( $this->tmpDir . 'extracted' ) )
        {
            ( new Filesystem() )->remove( [ $this->tmpDir . 'extracted' ] );
        }
    }

    /**
     * Test backup manager instantiated.
     */
    public function testBackupManagerInstantiatedCorrectly()
    {
        $this->assertNotNull( $this->manager );
    }

    /**
     * Test backup with pre- and post- backup events.
     *
     * @group slow
     */
    public function testBackupWithPreAndPostBackupEvent()
    {
        $this->manager->getEventDispatcher()->addListener( PreBackupEvent::class, function ( PreBackupEvent $event )
        {
            $this->assertInstanceOf( PreBackupEvent::class, $event );
            $this->assertNotNull( $event->getOptions() );
            $this->preVerified = TRUE;
        }
        );

        $this->manager->getEventDispatcher()->addListener( PostBackupEvent::class, function ( PostBackupEvent $event )
        {
            $this->assertInstanceOf( PostBackupEvent::class, $event );
            $this->assertNotNull( $event->getArchiveLocation() );
            $this->assertNotNull( $event->getOptions() );
            $this->postVerified = TRUE;
        }
        );

        $this->manager->backup( self::$backupOptions );

        $this->assertTrue( $this->preVerified );
        $this->assertTrue( $this->postVerified );
    }

    /**
     * Test backup and extract and check files.
     *
     * @group slow
     */
    public function testBackupAndCheckFiles()
    {
        $this->manager->getEventDispatcher()->addListener( PostBackupEvent::class, function ( PostBackupEvent $event )
        {
            $this->assertNotEmpty( $event->getArchiveLocation() );

            $zip = new ZipArchive();
            $zip->open( $event->getArchiveLocation() );

            $this->assertEquals( 'No error', $zip->getStatusString() );

            $dir = $this->tmpDir . 'extracted';

            if ( !file_exists( $dir ) )
            {
                mkdir( $dir );
            }

            $this->assertTrue( $zip->extractTo( $dir ) );

            $this->dirHasFile( $dir, 'bin' );
            $this->dirHasFile( $dir, 'res' );
            $this->dirHasFile( $dir, 'src' );
            $this->dirHasFile( $dir, 'src/Slicer' );
            $this->dirHasFile( $dir, 'src/Slicer/Manager/Backup' );
            $this->dirHasFile( $dir, 'src/Slicer/Command' );
            $this->dirHasFile( $dir, 'src/Slicer/Command/BackupCommand.php' );
            $this->dirHasFile( $dir, 'tests' );
            $this->dirHasFile( $dir, 'vendor' );
            $this->dirHasFile( $dir, 'composer.json' );
            $this->dirHasFile( $dir, 'composer.lock' );
            $this->dirHasFile( $dir, 'LICENSE' );
            $this->dirHasFile( $dir, 'phpunit.xml.dist' );
            $this->dirHasFile( $dir, 'slicer.json' );

            $this->postVerified = TRUE;
        }
        );

        $this->manager->backup( self::$backupOptions );

        $this->assertTrue( $this->postVerified );
    }

    /**
     * Helper method to check for file in a directory.
     *
     * @param string $dir
     * @param string $file
     */
    private function dirHasFile( $dir, $file )
    {
        $this->assertTrue( file_exists( clean_slicer_path( $dir . '/' . $file ) ) );
    }
}