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

namespace Slicer\Test\Manager\Update;

use Phar;
use Slicer\Config;
use Slicer\Factory;
use Slicer\Manager\Update\UpdateManager;
use Slicer\Provider\GitProvider;
use Slicer\TestCase;
use SplFileInfo;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

/**
 * Class UpdateManagerTest
 *
 * @package Slicer\Test\Manager\Update
 */
class UpdateManagerTest extends TestCase
{
    /** @var  UpdateManager */
    protected $manager;
    /** @var  bool */
    protected $preVerified;
    /** @var  bool */
    protected $postVerified;
    /** @var  string */
    protected $filesZipPath;
    /** @var  string */
    protected $updateClassName = 'Update';
    /** @var  string */
    protected $updateDir;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->updateDir = base_path( $this->config->getStorage()[ 'update-dir' ] ) . DIRECTORY_SEPARATOR;

        $this->manager = new UpdateManager( Factory::createConfig() );
        $this->manager->setEventDispatcher( new EventDispatcher() );
        $this->manager->setChangeProvider( new GitProvider() );

        $this->preVerified  = FALSE;
        $this->postVerified = FALSE;

        $this->filesZipPath = $this->tmpDir . 'test-files.zip';
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();

        unset( $this->manager );

        if ( file_exists( $this->filesZipPath ) )
        {
            unlink( $this->filesZipPath );
        }

        if ( file_exists( $this->tmpDir . 'extracted' ) )
        {
            ( new Filesystem() )->remove( [ $this->tmpDir . 'extracted' ] );
        }

        if ( file_exists( $this->tmpDir . $this->updateClassName . '.php' ) )
        {
            unlink( $this->tmpDir . $this->updateClassName . '.php' );
        }

        if ( file_exists( $this->updateDir . 'Update.phar' ) )
        {
            unlink( $this->updateDir . 'Update.phar' );
        }
    }

    /**
     * Test manager initialization.
     */
    public function testInitialization()
    {
        $this->assertNotNull( $this->manager );
        $this->assertNotNull( $this->manager->getChangeProvider() );
        $this->assertNotNull( $this->manager->getEventDispatcher() );
    }

    /**
     * Test zipping up changed files.
     */
    public function testZipChangedFiles()
    {
        $files = $this->getChangedFiles();

        $this->assertTrue( $this->manager->zipChangedFiles( $this->filesZipPath, $files ) );

        $this->assertTrue( file_exists( $this->filesZipPath ) );

        $zip = new ZipArchive();

        $zip->open( $this->filesZipPath );

        @mkdir( $this->tmpDir . 'extracted' );

        $this->assertTrue( $zip->extractTo( $this->tmpDir . 'extracted' ) );

        $this->assertTrue( file_exists( $this->tmpDir . 'extracted/.gitignore' ) );
        $this->assertTrue( file_exists( $this->tmpDir . 'extracted/slicer.json' ) );
        $this->assertTrue( file_exists( $this->tmpDir . 'extracted/src/bootstrap.php' ) );
    }

    /**
     * Test creating update class.
     */
    public function testCreateUpdateClass()
    {
        $files = $this->getChangedFiles();

        $this->assertTrue( $this->manager->createUpdateClass( $this->updateClassName, $this->tmpDir, $files ) );
    }

    /**
     * Test generating an update stub.
     */
    public function testGenerateUpdateStub()
    {
        $pharFile = 'update.phar';

        $stub = $this->manager->generateUpdateStub( $pharFile );

        $this->assertContains( "Phar::mapPhar( '{$pharFile}' );", $stub );
        $this->assertContains( "require 'phar://{$pharFile}/bin/update';", $stub );
    }

    /**
     * Test compiling update phar.
     */
    public function testCompileUpdatePhar()
    {
        $files = $this->getChangedFiles();

        $this->assertTrue( $this->manager->zipChangedFiles( $this->filesZipPath, $files ) );
        $this->assertTrue( $this->manager->createUpdateClass( $this->updateClassName, $this->tmpDir, $files ) );

        $classFile = new SplFileInfo( $this->tmpDir . $this->updateClassName . '.php' );
        $zipFile   = new SplFileInfo( $this->filesZipPath );

        $this->assertTrue( $this->manager->compileUpdatePhar( $classFile, $zipFile ) );
        $this->assertTrue( file_exists( $this->updateDir . 'Update.phar' ) );

        $phar = new Phar( $this->updateDir . 'Update.phar' );

        $this->assertTrue( $phar->valid() );

        //echo $phar->getStub();

        mkdir( $this->tmpDir . 'extracted' );

        $this->assertTrue( $phar->extractTo( $this->tmpDir . 'extracted' ) );
        $this->assertTrue( file_exists( $this->tmpDir . 'extracted' ) );
        $this->assertTrue( file_exists( $this->tmpDir . 'extracted/bin/update' ) );
        $this->assertTrue( file_exists( $this->tmpDir . 'extracted/res/files.zip' ) );
        $this->assertTrue( file_exists( $this->tmpDir . 'extracted/Update.php' ) );

        unset( $phar );
    }

    /**
     * Helper method to get changed files.
     *
     * @return array
     */
    protected function getChangedFiles()
    {
        return
            [
                'added'    => [ base_path( '.gitignore' ), ],
                'modified' => [ base_path( 'slicer.json' ), base_path( 'src/bootstrap.php' ) ],
                'deleted'  => [ ],
            ];
    }
}