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

namespace Slicer\Test;

use Slicer\Config;
use Slicer\Factory;
use Slicer\TestCase;

/**
 * Class FactoryTest
 *
 * @package Slicer\Test
 */
class FactoryTest extends TestCase
{
    /**
     * Test create config.
     */
    public function testCreateConfig()
    {
        $config = Factory::createConfig();

        $this->assertInstanceOf( Config::class, $config );
        $this->assertEquals( 'slicer/slicer', $config->getAppName() );
        $this->assertEquals( clean_slicer_path( __DIR__ . '/../../../' ), $config->getBaseDir() );
    }

    /**
     * Ensure slicer is always the same - singleton.
     */
    public function testSlicerIsSingleton()
    {
        $slicer1 = Factory::create();
        $slicer2 = Factory::create();

        $this->assertSame( $slicer1, $slicer2 );
    }

    /**
     * Test create slicer.
     */
    public function testCreateSlicer()
    {
        $slicer = Factory::create();

        $this->assertNotNull( $slicer );
        $this->assertInstanceOf( Config::class, $slicer->getConfig() );
    }
}