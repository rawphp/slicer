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

use Slicer\Manager\Installer\NonInteractiveFileBuilder;
use Slicer\TestCase;

/**
 * Class NonInteractiveFileBuilderTest
 *
 * @package Slicer\Test\Manager\Installer
 */
class NonInteractiveFileBuilderTest extends TestCase
{
    /** @var  NonInteractiveFileBuilder */
    protected $builder;

    /**
     * Setup before each test.
     */
    public function setUp()
    {
        $this->builder = new NonInteractiveFileBuilder();
    }

    /**
     * Test builder is initialized.
     */
    public function testInitialized()
    {
        $this->assertNotNull( $this->builder );
    }

    /**
     * Test constructing default slicer file structure.
     */
    public function testConstructFileStructure()
    {
        $file = $this->builder->constructFileStructure();

        $this->assertTrue( is_array( $file ) );
    }

    /**
     * Test building file.
     */
    public function testBuildFile()
    {
        $content = $this->builder->buildFile();

        $this->assertTrue( is_string( $content ) );
        $this->assertContains( '"app":', $content );
        $this->assertContains( '"options":', $content );
        $this->assertContains( '"update-file":', $content );
        $this->assertContains( '"change-provider":', $content );
        $this->assertContains( '"signing":', $content );
        $this->assertContains( '"storage":', $content );
        $this->assertContains( '"backup":', $content );
        $this->assertContains( '"base-dir":', $content );
    }
}