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

namespace Slicer\Test\Manager\Installer;

use InvalidArgumentException;
use Slicer\Contract\ISlicerFileBuilder;
use Slicer\Factory;
use Slicer\Manager\Installer\InstallationManager;
use Slicer\Manager\Installer\InteractiveFileBuilder;
use Slicer\Manager\Installer\NonInteractiveFileBuilder;
use Slicer\TestCase;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class InstallationManagerTest
 *
 * @package Slicer\Test\Manager\Installer
 */
class InstallationManagerTest extends TestCase
{
    /** @var  InstallationManager */
    protected $manager;
    /** @var  string */
    protected $fileName = 'test-slicer.json';

    /**
     * Setup before each test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->manager = new InstallationManager( Factory::createConfig() );
        $this->manager->setEventDispatcher( new EventDispatcher() );
        $this->manager->setFilename( $this->fileName );
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();

        if ( file_exists( $this->fileName ) )
        {
            unlink( $this->fileName );
        }
    }

    /**
     * Test initialized successfully.
     */
    public function testInitialization()
    {
        $this->assertNotNull( $this->manager );
        $this->assertEquals( $this->fileName, $this->manager->getFilename() );
    }

    /**
     * Test install without a file builder.
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Slicer\Contract\ISlicerFileBuilder must be set before running install()
     */
    public function testInstallWithoutSettingFileBuilder()
    {
        $this->manager->install();
    }

    /**
     * Test install with builders.
     *
     * @param ISlicerFileBuilder $builder
     *
     * @dataProvider dataForFileBuilders
     * @group        interactive
     */
    public function testInstallWithFileBuilder( $builder )
    {
        $this->manager->setFileBuilder( $builder );

        $this->assertTrue( $this->manager->install() );

        $this->assertTrue( file_exists( $this->fileName ) );

        $content = file_get_contents( $this->fileName );

        $this->assertTrue( is_string( $content ) );
        $this->assertContains( '"app":', $content );
        $this->assertContains( '"options":', $content );
        $this->assertContains( '"update_file":', $content );
        $this->assertContains( '"change_provider":', $content );
        $this->assertContains( '"signing":', $content );
        $this->assertContains( '"storage":', $content );
        $this->assertContains( '"backup":', $content );
        $this->assertContains( '"base_dir":', $content );
    }

    /**
     * Test setting file builders.
     *
     * @param ISlicerFileBuilder $builder
     *
     * @dataProvider dataForFileBuilders
     */
    public function testSetFileBuilders( $builder )
    {
        $this->manager->setFileBuilder( $builder );

        $this->assertNotNull( $this->manager->getFileBuilder() );
        $this->assertInstanceOf( 'Slicer\\Contract\\ISlicerFileBuilder', $this->manager->getFileBuilder() );
    }

    /**
     * Data provider for testSetFileBuilders().
     *
     * @return array
     */
    public function dataForFileBuilders()
    {
        return
            [
                [ new NonInteractiveFileBuilder() ],
                [ new InteractiveFileBuilder( new ArrayInput( [ ] ), new ConsoleOutput(), new QuestionHelper() ) ],
            ];
    }
}