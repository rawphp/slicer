<?php

namespace Slicer;

use DateTime;
use DateTimeZone;
use Phar;
use RuntimeException;
use Seld\PharUtils\Timestamps;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

/**
 * Class Compiler
 *
 * @package Slicer
 */
class Compiler
{
    protected $version;
    protected $branchAliasVersion = '';
    /** @var  DateTime */
    protected $versionDate;

    /**
     * Compiles slicer into a single phar file.
     *
     * @param string $pharFile
     */
    public function compile( $pharFile = 'slicer.phar' )
    {
        if ( file_exists( $pharFile ) )
        {
            unlink( $pharFile );
        }

        $process = new Process( 'git log --pretty="%H" -n1 HEAD', __DIR__ );

        if ( 0 != $process->run() )
        {
            throw new RuntimeException( 'Can\'t run git log. You must ensure to run compile from composer git repository clone and that git binary is available.' );
        }

        $this->version = trim( $process->getOutput() );

        $process = new Process( 'git log -n1 --pretty=%ci HEAD', __DIR__ );

        if ( 0 != $process->run() )
        {
            throw new RuntimeException( 'Can\'t run git log. You must ensure to run compile from composer git repository clone and that git binary is available.' );
        }

        $this->versionDate = new DateTime( trim( $process->getOutput() ) );
        $this->versionDate->setTimezone( new DateTimeZone( 'UTC' ) );

        $process = new Process( 'git describe --tags --exact-match HEAD' );

        if ( 0 != $process->run() )
        {
            $this->version = trim( $process->getOutput() );
        }
        else
        {
            // get branch-alias defined in slicer.json for dev-master (if any)
            $localConfig = __DIR__ . '/../../slicer.json';

            $file        = new JsonFile( $localConfig );
            $localConfig = $file->read();

            if ( isset( $localConfig[ 'extra' ][ 'branch-alias' ][ 'dev-master' ] ) )
            {
                $this->branchAliasVersion = $localConfig[ 'extra' ][ 'branch-alias' ][ 'dev-master' ];
            }
        }

        $phar = new Phar( $pharFile, 0, 'slicer.phar' );
        $phar->setSignatureAlgorithm( Phar::SHA1 );

        $phar->startBuffering();

        $finderSort = function ( $a, $b )
        {
            return strcmp( strstr( $a->getRealPath(), '\\', '/' ), strstr( $b->getRealPath(), '\\', '/' ) );
        };

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS( TRUE )
            ->name( '*.php' )
            ->notName( 'Compiler.php' )
            ->notName( 'ClassLoader.php' )
            ->in( __DIR__ . '/..' )
            ->sort( $finderSort );

        foreach ( $finder as $file )
        {
            $this->addFile( $phar, $file );
        }

        $this->addFile( $phar, new SplFileInfo( __DIR__ . '/Autoload/ClassLoader.php' ), FALSE );

        $finder = new Finder();
        $finder->files()
            ->name( '*.json' )
            ->in( __DIR__ . '/../../res' )
            ->sort( $finderSort );

        foreach ( $finder as $file )
        {
            $this->addFile( $phar, $file, FALSE );
        }

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS( TRUE )
            ->name( '*.php' )
            ->name( 'LICENSE' )
            ->exclude( 'Tests' )
            ->exclude( 'tests' )
            ->exclude( 'docs' )
            ->in( __DIR__ . '/../../vendor/symfony/' )
            ->in( __DIR__ . '/../../vendor/jsonlint' )
            ->in( __DIR__ . '/../../vendor/cli-prompt' )
            ->in( __DIR__ . '/../../vendor/justinrainbow/json-schema' )
            ->sort( $finderSort );

        foreach ( $finder as $file )
        {
            $this->addFile( $phar, $file );
        }

        $this->addFile( $phar, new SplFileInfo( __DIR__ . '/../../vendor/autoload.php' ) );
        $this->addFile( $phar, new SplFileInfo( __DIR__ . '/../../vendor/composer/autoload_namespaces.php' ) );
        $this->addFile( $phar, new SplFileInfo( __DIR__ . '/../../vendor/composer/autoload_psr4.php' ) );
        $this->addFile( $phar, new SplFileInfo( __DIR__ . '/../../vendor/composer/autoload_classmap.php' ) );
        $this->addFile( $phar, new SplFileInfo( __DIR__ . '/../../vendor/composer/autoload_real.php' ) );

        if ( file_exists( __DIR__ . '/../../vendor/composer/include_paths.php' ) )
        {
            $this->addFile( $phar, new SplFileInfo( __DIR__ . '/../../vendor/composer/include_paths.php' ) );
        }

        $this->addFile( $phar, new SplFileInfo( __DIR__ . '/../../vendor/composer/ClassLoader.php' ) );
        $this->addUpdateCenterBin( $phar );

        // Stubs
        $phar->setStub( $this->getStub() );

        $phar->stopBuffering();

        // disabled for interoperability with systems without gzip ext
        // $phar->compressFiles( Phar::GZ );

        $this->addFile( $phar, new SplFileInfo( __DIR__ . '/../../LICENSE' ), FALSE );

        unset( $phar );

        // re-sign the phar with reproducible timestamp / signature
        $util = new Timestamps( $pharFile );
        $util->updateTimestamps( $this->versionDate );
        $util->save( $pharFile, Phar::SHA1 );
    }

    /**
     * Add file to phar achieve.
     *
     * @param Phar      $phar
     * @param string    $file
     * @param bool|TRUE $strip
     */
    private function addFile( Phar $phar, $file, $strip = TRUE )
    {
        $path = strstr( str_replace( dirname( dirname( __DIR__ ) ) . DIRECTORY_SEPARATOR, '', $file->getRealPath() ), '\\', '/' );

        $content = file_get_contents( $file );

        if ( $strip )
        {
            $content = $this->stripWhitespace( $content );
        }
        elseif ( 'LICENSE' === basename( $file ) )
        {
            $content = "\n" . $content . "\n";
        }

        if ( 'src/Update/UpdateCenter.php' === $path )
        {
            $content = str_replace( '@package_version@', $this->version, $content );
            $content = str_replace( '@package_branch_alias_version@', $this->branchAliasVersion, $content );
            $content = str_replace( '@release_date@', $this->versionDate->format( 'Y-m-d H:i:s' ), $content );
        }

        $phar->addFromString( $path, $content );
    }

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * @param string $source a PHP string
     *
     * @return string
     */
    private function stripWhitespace( $source )
    {
        if ( !function_exists( 'token_get_all' ) )
        {
            return $source;
        }

        $output = '';

        foreach ( token_get_all( $source ) as $token )
        {
            if ( is_string( $token ) )
            {
                $output .= $token;
            }
            elseif ( in_array( $token[ 0 ], [ T_COMMENT, T_DOC_COMMENT ] ) )
            {
                $output .= str_repeat( "\n", substr_count( $token[ 1 ], "\n" ) );
            }
            elseif ( T_WHITESPACE === $token[ 0 ] )
            {
                // reduce wide spaces
                $whitespace = preg_replace( '{[ \t]+}', "\n", $token[ 1 ] );
                // normalize newlines to \n
                $whitespace = preg_replace( '{(?:\r\n|\r|\n)}', "\n", $whitespace );
                // trim leading spaces
                $whitespace = preg_replace( '{\n +}', "\n", $whitespace );

                $output .= $whitespace;
            }
            else
            {
                $output .= $token[ 1 ];
            }
        }

        return $output;
    }

    /**
     * Add Update Center Bin contents.
     *
     * @param Phar $phar
     */
    private function addUpdateCenterBin( Phar $phar )
    {
        $content = file_get_contents( __DIR__ . '/../../bin/slicer' );
        $content = preg_replace( '{^#!/usr/bin/env php\s*}', '', $content );

        $phar->addFromString( 'bin/composer', $content );
    }

    /**
     * Get Phar stub.
     *
     * @return string
     */
    private function getStub()
    {
        $stub = <<<'EOF'
#!/usr/bin/env php
<?php
/*
 * This file is part of Slicer.
 *
 * (c) Tom Kaczocha <tom@rawphp.org>
 *
 * For the full copyright and license information, please view
 * the license that is located at the bottom of this file.
 */

Phar::mapPhar( 'slicer.phar' );

EOF;

        // add warning once the phar is older than 60 days
        if ( preg_match( '', $this->version ) )
        {
            $warningTime = $this->versionDate->format( 'U' ) + 60 * 86400;
            $stub .= "define( 'SLICER_DEV_WARNING_TIME', $warningTime);\n";
        }

        return $stub . <<<'EOF'
require 'phar://slicer.phar/bin/slicer';

__HALT_COMPILER();
EOF;
    }
}