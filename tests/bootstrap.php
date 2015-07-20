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

error_reporting( E_ALL );

if ( function_exists( 'date_default_timezone_set' ) && function_exists( 'date_default_timezone_get' ) )
{
    date_default_timezone_set( @date_default_timezone_get() );
}

require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/Slicer/TestCase.php';
