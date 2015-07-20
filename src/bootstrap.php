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

if ( !function_exists( 'includeIfExists' ) )
{
    function includeIfExists( $file )
    {
        return file_exists( $file ) ? include $file : FALSE;
    }
}

if ( ( !$loader = includeIfExists( __DIR__ . '/../vendor/autoload.php' ) ) && ( !$loader = includeIfExists( __DIR__ . '/../../../autoload.php' ) ) )
{
    echo 'You must set up the project dependencies, run the following commands:' . PHP_EOL .
        'curl -sS https://getslicer.com/installer | php' . PHP_EOL .
        'php slicer.phar install' . PHP_EOL;

    exit( 1 );
}

return $loader;