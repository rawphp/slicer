<?php

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