<?php

namespace Slicer\Generator;

/**
 * Class ClassGenerator
 *
 * @package Slicer\Generator
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
}