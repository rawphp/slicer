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

if ( !function_exists( 'base_path' ) )
{
    /**
     * Get the path to the base of the install.
     *
     * @param  string $path
     *
     * @return string
     */
    function base_path( $path = '' )
    {
        $dir = ( isset( $_SERVER[ 'PWD' ] ) ? $_SERVER[ 'PWD' ] : __DIR__ . '/../' );

        if ( !file_exists( clean_slicer_path( $dir . '/slicer.json' ) ) && !file_exists( clean_slicer_path( $dir . 'slicer/tmp' ) ) )
        {
            echo PHP_EOL . PHP_EOL . 'Unable to find slicer.json file in current directory' . PHP_EOL . 'Place a slicer.json file in this directory if you want this to be the base directory for this application.' . PHP_EOL . PHP_EOL;

            exit( 1 );
        }

        $path = strtr( str_replace( [ 'phar://', 'update.phar' ], '', $dir ) . ( $path ? DIRECTORY_SEPARATOR . $path : $path ), '\\', '/' );

        $path = strtoupper( substr( $path, 0, 1 ) ) . substr( $path, 1 );

        return clean_slicer_path( $path );
    }
}

if ( !function_exists( 'get_slicer_config' ) )
{
    /**
     * Returns the application configuration array.
     *
     * @return array
     */
    function get_slicer_config()
    {
        $file = base_path( 'slicer.json' );

        if ( file_exists( $file ) )
        {
            return json_decode( file_get_contents( $file ), TRUE );
        }
        else
        {
            echo PHP_EOL . 'Unable to find "slicer.json". Are you sure you are in the root directory?' . PHP_EOL;

            exit( 1 );
        }
    }
}

if ( !function_exists( 'clean_slicer_path' ) )
{
    /**
     * Clean a path.
     *
     * @param string $path
     *
     * @return string
     */
    function clean_slicer_path( $path )
    {
        $parts = explode( DIRECTORY_SEPARATOR, strtr( $path, '/', DIRECTORY_SEPARATOR ) );

        $count = 0;

        $nParts = [ ];

        foreach ( $parts as $part )
        {
            if ( '..' === trim( $part ) )
            {
                $count++;
            }
            else
            {
                $nParts[] = trim( $part );
            }
        }

        $path = '';

        $i = 0;

        $add   = ( $count > 0 ) ? 1 : 0;
        $count = ( 2 * $count + $add );

        while ( $i < count( $parts ) - $count )
        {
            $path .= $nParts[ $i ] . DIRECTORY_SEPARATOR;

            $i++;
        }

        return rtrim( $path, DIRECTORY_SEPARATOR );
    }
}
