<?php

namespace Slicer\Provider;

use Exception;
use Slicer\Provider\Contract\IChangeProvider;

/**
 * Class GitProvider
 *
 * @package Slicer\Provider
 */
class GitProvider implements IChangeProvider
{
    /**
     * Get a collection of changed files.
     *  keys =
     *      - added
     *      - modified
     *      - deleted
     *
     * @param string $baseDir base directory
     * @param string $from    from hash
     * @param string $to      to hash
     *
     * @return array
     */
    public function getChangedFiles( $baseDir, $from, $to )
    {
        $patchFile = 'patch-' . str_random( 8 ) . '.tmp';

        if ( file_exists( $patchFile ) )
        {
            unlink( $patchFile );
        }

        $baseDir = str_replace( '/', DIRECTORY_SEPARATOR, trim( $baseDir ) );

        if ( DIRECTORY_SEPARATOR !== $baseDir[ strlen( $baseDir ) - 1 ] )
        {
            $baseDir .= DIRECTORY_SEPARATOR;
        }

        try
        {
            $result = [ 'added' => [ ], 'modified' => [ ], 'deleted' => [ ] ];

            $cmd = 'git diff --name-status --patch';

            $cmd = $cmd . " $from $to";
            $cmd = $cmd . ' > ' . $patchFile;
            $out = [ ];
            $ret = NULL;

            exec( $cmd, $out, $ret );

            $output = file_get_contents( $patchFile );

            $files = explode( "\n", $output );

            foreach ( $files as $file )
            {
                if ( 0 === strlen( trim( $file ) ) )
                {
                    continue;
                }

                list( $type, $path ) = explode( "\t", $file );

                switch ( trim( $type ) )
                {
                    case 'A':
                        $result[ 'added' ][] = str_replace( '/', DIRECTORY_SEPARATOR, $baseDir . $path );
                        break;
                    case 'M':
                        $result[ 'modified' ][] = str_replace( '/', DIRECTORY_SEPARATOR, $baseDir . $path );
                        break;
                    case 'D':
                        $result[ 'deleted' ][] = str_replace( '/', DIRECTORY_SEPARATOR, $baseDir . $path );
                        break;
                    case 'C':
                    case 'R':
                        list( $from, $to ) = explode( "\t", $path );
                        $result[ 'deleted' ][] = str_replace( '/', DIRECTORY_SEPARATOR, $baseDir . $from );
                        $result[ 'added' ][]   = str_replace( '/', DIRECTORY_SEPARATOR, $baseDir . $to );
                        break;
                    default:
                        syslog( LOG_ALERT, 'Unknown Value - Type: ' . $type . ' - Path: ' . $path );
                        break;
                }
            }

            if ( file_exists( $patchFile ) )
            {
                unlink( $patchFile );
            }

            return $result;
        }
        catch ( Exception $e )
        {
            echo $e->getMessage() . PHP_EOL;

            if ( file_exists( $patchFile ) )
            {
                unlink( $patchFile );
            }
        }

        return NULL;
    }
}