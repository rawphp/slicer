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

namespace Slicer\Test\Provider;

use Slicer\Provider\GitProvider;
use Slicer\TestCase;

/**
 * Class GitProviderTest
 *
 * @package Slicer\Test\Provider
 */
class GitProviderTest extends TestCase
{
    /** @var  GitProvider */
    protected $provider;

    protected $startHash = '798beb8c2d18a95b895c0b62e1d306df34741993';
    protected $endHash = '6b44bc0ca751f814c37b869e130a9fc33b5f4edb';

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->provider = new GitProvider();
    }

    /**
     * Test initialization.
     */
    public function testInitialization()
    {
        $this->assertNotNull( $this->provider );
    }

    /**
     * Test get changed files.
     */
    public function testGetChangedFiles()
    {
        $files = $this->provider->getChangedFiles( base_path(), $this->startHash, $this->endHash );

        $this->assertTrue( is_array( $files ) );
        $this->assertNotEmpty( $files );
        $this->assertArrayHasKey( 'added', $files );
        $this->assertArrayHasKey( 'modified', $files );
        $this->assertArrayHasKey( 'deleted', $files );
    }
}