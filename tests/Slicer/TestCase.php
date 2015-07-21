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

namespace Slicer;

use PHPUnit_Framework_TestCase;

/**
 * Class TestCase
 *
 * @package Slicer
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{
    /** @var  Config */
    protected $config;
    /** @var  string */
    protected $tmpDir;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->config = $this->getConfig();

        $this->tmpDir = base_path( $this->config->getStorage()[ 'tmp-dir' ] ) . DIRECTORY_SEPARATOR;
    }

    /**
     * Get configuration for tests.
     *
     * @return Config
     */
    protected function getConfig()
    {
        return Factory::createConfig();
    }
}
