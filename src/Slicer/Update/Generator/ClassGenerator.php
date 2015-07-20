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

namespace Slicer\Update\Generator;

/**
 * Class ClassGenerator
 *
 * @package Slicer\Update\Generator
 */
class ClassGenerator
{
    public static function generateUpdateFilesMethod( $updateName, $baseDir, array $files )
    {
        $text = '    /**' . PHP_EOL;
        $text .= '     * Copy new and updated files into the project.' . PHP_EOL;
        $text .= '     *' . PHP_EOL;
        $text .= '     * @return bool' . PHP_EOL;
        $text .= '     */' . PHP_EOL;
        $text .= '    public function updateFiles( )' . PHP_EOL;
        $text .= '    {' . PHP_EOL;
        $text .= '        try' . PHP_EOL;
        $text .= '        {' . PHP_EOL;

        $text .= '            $files =' . PHP_EOL;
        $text .= '            [' . PHP_EOL;

        foreach ( $files as $file )
        {
            $text .= "                storage_path( '/auto-update/updates/" . $updateName . str_replace( $baseDir, '', $file ) . "' ) => base_path( " . "'" . str_replace( $baseDir, '', $file ) . "'" . " )," . PHP_EOL;
        }

        $text .= '            ];' . PHP_EOL;
        $text .= PHP_EOL;
        $text .= '            foreach( $files as $source => $destination )' . PHP_EOL;
        $text .= '            {' . PHP_EOL;
        $text .= '                if ( file_exists( $source ) )' . PHP_EOL;
        $text .= '                {' . PHP_EOL;
        $text .= '                    copy( $source, $destination );' . PHP_EOL;
        $text .= '                }' . PHP_EOL;
        $text .= '            }' . PHP_EOL;
        $text .= '        }' . PHP_EOL;
        $text .= '        catch( Exception $e )' . PHP_EOL;
        $text .= '        {' . PHP_EOL;
        $text .= '            Log::error( $e );' . PHP_EOL;
        $text .= '        }' . PHP_EOL;
        $text .= '    }' . PHP_EOL;

        return $text;
    }

    public static function generateDeleteFilesMethod( array $files )
    {
        $text = '    /**' . PHP_EOL;
        $text .= '     * Delete old files.' . PHP_EOL;
        $text .= '     *' . PHP_EOL;
        $text .= '     * @return bool' . PHP_EOL;
        $text .= '     */' . PHP_EOL;
        $text .= '    public function deleteFiles( )' . PHP_EOL;
        $text .= '    {' . PHP_EOL;
        $text .= '        try' . PHP_EOL;
        $text .= '        {' . PHP_EOL;

        $text .= '            $files =' . PHP_EOL;
        $text .= '            [' . PHP_EOL;

        foreach ( $files as $file )
        {
            $text .= '                base_path( "' . $file . '" ),' . PHP_EOL;
        }

        $text .= '            ];' . PHP_EOL;
        $text .= PHP_EOL;
        $text .= '            foreach( $files as $file )' . PHP_EOL;
        $text .= '            {' . PHP_EOL;
        $text .= '                if ( file_exists( $file ) )' . PHP_EOL;
        $text .= '                {' . PHP_EOL;
        $text .= '                    unlink( $file );' . PHP_EOL;
        $text .= '                }' . PHP_EOL;
        $text .= '            }' . PHP_EOL;
        $text .= '        }' . PHP_EOL;
        $text .= '        catch( Exception $e )' . PHP_EOL;
        $text .= '        {' . PHP_EOL;
        $text .= '            Log::error( $e );' . PHP_EOL;
        $text .= '        }' . PHP_EOL;
        $text .= '    }' . PHP_EOL;

        return $text;
    }

    public static function generateUpdateDatabaseMethod()
    {
        $text = '    /**' . PHP_EOL;
        $text .= '     * Update database.' . PHP_EOL;
        $text .= '     *' . PHP_EOL;
        $text .= '     * @return bool' . PHP_EOL;
        $text .= '     */' . PHP_EOL;
        $text .= '    public function updateDatabase()' . PHP_EOL;
        $text .= '    {' . PHP_EOL;
        $text .= PHP_EOL;
        $text .= '    }' . PHP_EOL;

        return $text;
    }

    public static function generateRollbackUpdateFilesMethod()
    {
        $text = '    /**' . PHP_EOL;
        $text .= '     * Rollback update file changes.' . PHP_EOL;
        $text .= '     *' . PHP_EOL;
        $text .= '     * @return bool' . PHP_EOL;
        $text .= '     */' . PHP_EOL;
        $text .= '    public function rollbackUpdateFiles()' . PHP_EOL;
        $text .= '    {' . PHP_EOL;
        $text .= PHP_EOL;
        $text .= '    }' . PHP_EOL;

        return $text;
    }

    public static function generateRollbackDeleteFilesMethod()
    {
        $text = '    /**' . PHP_EOL;
        $text .= '     * Rollback deleted files.' . PHP_EOL;
        $text .= '     *' . PHP_EOL;
        $text .= '     * @return bool' . PHP_EOL;
        $text .= '     */' . PHP_EOL;
        $text .= '    public function rollbackDeleteFiles()' . PHP_EOL;
        $text .= '    {' . PHP_EOL;
        $text .= PHP_EOL;
        $text .= '    }' . PHP_EOL;

        return $text;
    }

    public static function generateRollbackDatabaseChangesMethod()
    {
        $text = '    /**' . PHP_EOL;
        $text .= '     * Rollback any database updates.' . PHP_EOL;
        $text .= '     *' . PHP_EOL;
        $text .= '     * @return bool' . PHP_EOL;
        $text .= '     */' . PHP_EOL;
        $text .= '    public function rollbackDatabaseChanges()' . PHP_EOL;
        $text .= '    {' . PHP_EOL;
        $text .= PHP_EOL;
        $text .= '    }' . PHP_EOL;

        return $text;
    }
}