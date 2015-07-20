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
