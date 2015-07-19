<?php

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
