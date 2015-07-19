<?php

namespace Slicer;

use Slicer\Backup\BackupManager;
use Slicer\Downloader\DownloadManager;
use Slicer\Installer\InstallationManager;
use RuntimeException;
use Slicer\Updater\UpdateManager;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Factory
 *
 * @package Slicer
 */
class Factory
{
    /** @var  Slicer */
    private static $instance;

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
     * Get cache directory.
     *
     * @param string $home
     *
     * @return string
     */
    protected static function getCacheDir( $home )
    {
        $cacheDir = getenv( 'SLICER_CACHE_DIR' );

        if ( !$cacheDir )
        {
            if ( defined( 'PHP_WINDOWS_VERSION_MAJOR' ) )
            {
                if ( $cacheDir = getenv( 'LOCALAPPDATA' ) )
                {
                    $cacheDir .= '/Slicer';
                }
                else
                {
                    $cacheDir = $home . '/cache';
                }

                $cacheDir = strtr( $cacheDir, '\\', '/' );
            }
            else
            {
                $cacheDir = $home . '/cache';
            }
        }

        return $cacheDir;
    }

    /**
     * Create config.
     *
     * @param string $cwd
     *
     * @return Config
     */
    public static function createConfig( $cwd = NULL )
    {
        $cwd = $cwd ?: getcwd();

        // determine home and cache dirs
        $home     = $cwd; // self::getHomeDir();
        $cacheDir = self::getCacheDir( $home );

        // protect directory against web access. Since HOME could be
        // the www-data's user home and be web-accessible it is a
        // potential security risk
        foreach ( [ $home, $cacheDir ] as $dir )
        {
            if ( !file_exists( $dir . '/.htaccess' ) )
            {
                if ( !is_dir( $dir ) )
                {
                    @mkdir( $dir, 0777, TRUE );
                }

                @file_put_contents( $dir . '/.htaccess', 'Deny from all' );
            }
        }

        $settings = json_decode( file_get_contents( Factory::getSlicerFile() ), TRUE );

        $settings[ 'home' ]      = $home;
        $settings[ 'cache_dir' ] = $cacheDir;
        $settings[ 'cwd' ]       = $cwd;

        if ( '' === $settings[ 'base_dir' ] )
        {
            $settings[ 'base_dir' ] = $cwd;
        }

        $config = new Config( $settings );

        return $config;
    }

    /**
     * Get the project slicer file.
     *
     * @return string
     */
    public static function getSlicerFile()
    {
        return trim( getenv( 'SLICER' ) ) ?: './slicer.json';
    }

    /**
     * Create a Slicer instance.
     *
     * @return Slicer
     */
    public function createSlicer()
    {
        $config = Factory::createConfig();

        $slicer = new Slicer();
        $slicer->setConfig( $config );
        $slicer->setEventDispatcher( new EventDispatcher() );

        $slicer->setEventDispatcher( $slicer->getEventDispatcher() );

        $slicer->setUpdateManager( new UpdateManager( $config ) );
        $slicer->setDownloadManager( new DownloadManager( $config ) );
        $slicer->setBackupManager( new BackupManager( $config ) );
        $slicer->setInstallationManager( new InstallationManager( $config ) );

        $slicer->getUpdateManager()
            ->setDownloadManager( $slicer->getDownloadManager() )
            ->setEventDispatcher( $slicer->getEventDispatcher() );
        $slicer->getDownloadManager()
            ->setEventDispatcher( $slicer->getEventDispatcher() );
        $slicer->getBackupManager()
            ->setEventDispatcher( $slicer->getEventDispatcher() );
        $slicer->getInstallationManager()
            ->setEventDispatcher( $slicer->getEventDispatcher() );

        return $slicer;
    }

    /**
     * Create new factory instance.
     *
     * @return Slicer
     */
    public static function create()
    {
        $factory = new static();

        if ( NULL === Factory::$instance )
        {
            Factory::$instance = $factory->createSlicer();
        }

        return Factory::$instance;
    }
}