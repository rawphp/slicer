<?php

namespace Slicer;

use Composer\IO\IOInterface;
use Slicer\Downloader\DownloadManager;
use Slicer\Installer\InstallationManager;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Factory
 *
 * @package Slicer
 */
class Factory
{
    /**
     * Get Slicer Home Directory.
     *
     * @return string
     */
    protected static function getHomeDir()
    {
        $home = getenv( 'SLICER_HOME' );

        if ( !$home )
        {
            if ( defined( 'PHP_WINDOWS_VERSION_MAJOR' ) )
            {
                if ( !getenv( 'APPDATA' ) )
                {
                    throw new RuntimeException( 'The APPDATA or SLICER_HOME environment variables must be set for slicer to run correctly' );
                }

                $home = strstr( getenv( 'APPDATA' ), '\\', '/' ) . '/Slicer';
            }
            else
            {
                if ( !getenv( 'HOME' ) )
                {
                    throw new RuntimeException( 'The HOME or SLICER_HOME environment variable must be set for slicer to run correctly' );
                }

                $home = rtrim( getenv( 'HOME' ), '/' ) . '/.slicer';
            }
        }

        return $home;
    }

    /**
     * Create a Slicer instance.
     *
     * @param array  $localConfig
     * @param string $cwd
     *
     * @return Slicer
     */
    public function createSlicer( $localConfig = NULL, $cwd = NULL )
    {
        $cwd = $cwd ?: getcwd();

        // load Slider configuration
        if ( NULL === $localConfig )
        {
            $localConfig = '';
        }

        $config = [ ];

        $slicer = new Slicer();
        $slicer->setConfig( $config );

        $dispatcher = new EventDispatcher();
        $slicer->setEventDispatcher( $dispatcher );

        $installer = new InstallationManager();
        $slicer->setInstallationManager( $installer );

        $downloader = new DownloadManager();
        $slicer->setDownloadManager( $downloader );

        return $slicer;
    }

    /**
     * Create new factory instance.
     *
     * @param IOInterface $io
     * @param array       $config
     *
     * @return Slicer
     */
    public static function create( IOInterface $io, $config = NULL )
    {
        $factory = new static();

        return $factory->createSlicer( $io, $config );
    }
}