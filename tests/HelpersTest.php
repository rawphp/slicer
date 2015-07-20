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

use Slicer\TestCase;

/**
 * Class HelpersTest
 */
class HelpersTest extends TestCase
{
    /**
     * Test getting correct base path.
     */
    public function testBasePath()
    {
        $this->assertEquals( clean_slicer_path( __DIR__ . '/../' ), base_path(), 'Paths are not equal', 0.0, 10, FALSE, TRUE );
    }

    /**
     * Test getting slicer config file.
     */
    public function testGetSlicerConfig()
    {
        $expected = [ 'app'     =>
                          [ 'name' => 'slicer/slicer' ],
                      'signing' =>
                          [ 'private_key' => 'private.key' ],
        ];

        $config = get_slicer_config();

        $this->assertEquals( $expected[ 'app' ][ 'name' ], $config[ 'app' ][ 'name' ] );
        $this->assertEquals( $expected[ 'signing' ][ 'private_key' ], $config[ 'signing' ][ 'private_key' ] );
    }

    /**
     * @dataProvider dataCleanSliderPaths
     *
     * @param string $expected
     * @param string $path
     */
    public function testCleanSliderPath( $expected, $path )
    {
        $this->assertEquals( $expected, clean_slicer_path( $path ) );
    }

    /**
     * Data provider for testCleanSliderPath()
     *
     * @return array
     */
    public function dataCleanSliderPaths()
    {
        return
            [
                [ __DIR__, __DIR__ ],
                [ dirname( dirname( __FILE__ ) ), __DIR__ . '/../' ],
                [ dirname( dirname( dirname( __FILE__ ) ) ), __DIR__ . '/../../' ],
            ];
    }
}