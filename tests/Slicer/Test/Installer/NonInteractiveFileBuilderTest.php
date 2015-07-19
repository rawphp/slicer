<?php

namespace Slicer\Test\Installer;

use Slicer\Installer\NonInteractiveFileBuilder;
use Slicer\TestCase;

/**
 * Class NonInteractiveFileBuilderTest
 *
 * @package Slicer\Test\Installer
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
        $this->assertContains( '"update_file":', $content );
        $this->assertContains( '"change_provider":', $content );
        $this->assertContains( '"signing":', $content );
        $this->assertContains( '"storage":', $content );
        $this->assertContains( '"backup":', $content );
        $this->assertContains( '"base_dir":', $content );
    }
}