<?php

namespace Slicer;

use DateTime;
use Exception;
use Slicer\Exception\InvalidChangeProvider;
use Slicer\Generator\ClassGenerator;
use Slicer\Provider\Contract\IChangeProvider;
use SplFileInfo;
use ZipArchive;

/**
 * Class UpdateService
 *
 * @package Slicer
 */
class UpdateService
{
    /** @var  IChangeProvider */
    protected $changesProvider;
    /** @var  string */
    protected $privateKey;
    /** @var  array */
    protected $config;
    /** @var  bool */
    protected $backupFilesBeforeUpdate;
    /** @var  bool */
    protected $backupDatabaseBeforeUpdate;
    /** @var  array */
    private $_cleanupFiles;
    /** @var  string */
    private $_tmpDir;

    /**
     * Create Auto-Update service.
     *
     * @param array $config
     */
    public function __construct( array $config )
    {
        $this->config = $config;

        $this->backupFilesBeforeUpdate    = $this->config[ 'backup-files-before-update' ];
        $this->backupDatabaseBeforeUpdate = $this->config[ 'backup-database-before-update' ];

        $this->initDirectories();
        $this->initChangesProvider();
        $this->initPrivateKey();
    }

    /**
     * Initialise provider directories.
     */
    protected function initDirectories()
    {
        $dirs =
            [
                $this->config[ 'storage' ][ 'source' ][ 'tmp-dir' ],
                $this->config[ 'storage' ][ 'destination' ][ 'updates-dir' ],
            ];

        foreach ( $dirs as $dir )
        {
            if ( !file_exists( $dir ) )
            {
                mkdir( $dir, 0777, TRUE );
            }
        }
    }

    /**
     * Initialise Git.
     */
    protected function initChangesProvider()
    {
        $class = $this->config[ 'change-provider' ][ 'class' ];

        if ( class_exists( $class ) )
        {
            $this->changesProvider = new $class();

            return;
        }

        throw new InvalidChangeProvider( "Change Provider '$class' does not exist." );
    }

    /**
     * Initialise the private key.
     */
    protected function initPrivateKey()
    {
        $file = $this->config[ 'private-key' ][ 'file' ];

        if ( file_exists( $file ) )
        {
            $this->privateKey = file_get_contents( $this->config[ 'private-key' ][ 'file' ] );
        }
    }

    /**
     * Create Update.
     *
     * @param string $from from hash
     * @param string $to   to hash
     */
    public function createUpdate( $from, $to )
    {
        $date = ( new DateTime() );

        $name = 'Update-' . $date->format( 'y-m-d-h-m-s' );

        $this->_tmpDir = str_replace( '/', DIRECTORY_SEPARATOR, $this->config[ 'storage' ][ 'source' ][ 'tmp-dir' ] . DIRECTORY_SEPARATOR );

        $className     = 'TestUpdate';// . $date->format( 'ymdhms' ) . '.php';
        $updateZipFile = $name . '.zip';
        $filesZip      = $this->_tmpDir . 'files.zip';

        try
        {
            // get changed files
            $files = $this->changesProvider->getChangedFiles( $this->config[ 'base_dir' ], $from, $to );

            if ( FALSE === $this->zipChangedFiles( $filesZip, $files ) )
            {
                throw new Exception( 'Failed to zip up changed files' );
            }

            // create update class
            if ( FALSE === $this->createUpdateClass( $className, $this->_tmpDir, $files ) )
            {
                throw new Exception( 'Failed to create new update file' );
            }

            // create update zip
            $this->createFinalUpdateZip( $updateZipFile, $this->_tmpDir . $className . '.php', $filesZip );
        }
        catch ( Exception $e )
        {
            echo $e->getMessage();
        }
    }

    /**
     * Run update.
     */
    public function runUpdate()
    {
        // find update

        // extract update

        // delete old files

        // copy new/updated files

        // add update to database

    }

    public function rollback()
    {

    }

    /**
     * Get config.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set config.
     *
     * @param array $config
     *
     * @return UpdateService
     */
    public function setConfig( array $config )
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get change provider.
     *
     * @return IChangeProvider
     */
    public function getChangesProvider()
    {
        return $this->changesProvider;
    }

    /**
     * Set change provider.
     *
     * @param IChangeProvider $changesProvider
     *
     * @return UpdateService
     */
    public function setChangesProvider( IChangeProvider $changesProvider )
    {
        $this->changesProvider = $changesProvider;

        return $this;
    }

    /**
     * Get private key.
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * Set private key.
     *
     * @param string $privateKey
     *
     * @return UpdateService
     */
    public function setPrivateKey( $privateKey )
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * Zip up changed files.
     *
     * @param string $filename
     * @param array  $files changed files
     *
     * @return bool
     */
    protected function zipChangedFiles( $filename, array $files )
    {
        // zip changed files
        $zip = new ZipArchive();

        if ( TRUE === $zip->open( $filename, ZipArchive::CREATE ) )
        {
            foreach ( array_merge( $files[ 'added' ], $files[ 'modified' ] ) as $file )
            {
                $info = new SplFileInfo( $file );

                $zip->addFile( $file, str_replace( $this->config[ 'base_dir' ], '', $info->getPathname() ) );
            }

            $zip->close();

            $this->_cleanupFiles[] = $filename;

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Create the update file.
     *
     * @param string $className
     * @param string $path
     * @param array  $changedFiles
     *
     * @return bool
     */
    protected function createUpdateClass( $className, $path, array $changedFiles )
    {
        $this->_cleanupFiles[] = $file = $path . $className . '.php';

        $snippetsDir = $this->config[ 'base_dir' ] . '/snippets/';

        $contents = file_get_contents( $snippetsDir . 'header.txt' );

        $changedFiles[ 'deleted' ][] = 'file.txt';

        $contents .= ClassGenerator::generateUpdateFilesMethod( $className, $this->config[ 'base_dir' ], array_merge( $changedFiles[ 'added' ], $changedFiles[ 'modified' ] ) );
        $contents .= PHP_EOL;
        $contents .= ClassGenerator::generateDeleteFilesMethod( $changedFiles[ 'deleted' ] );

        $contents .= file_get_contents( $snippetsDir . 'footer.txt' );

        $contents = str_replace( '{namespace}', $this->config[ 'update-file' ][ 'namespace' ], $contents );
        $contents = str_replace( '{class_name}', $className, $contents );

        return file_put_contents( $file, $contents );
    }

    /**
     * Cleanup files.
     */
    protected function cleanUp()
    {
        foreach ( $this->_cleanupFiles as $file )
        {
            if ( file_exists( $file ) )
            {
                unlink( $file );
            }
        }
    }

    /**
     * Create final update zip.
     *
     * @param string $updateZipFile
     * @param string $classFile
     * @param string $filesZip
     *
     * @return bool
     */
    protected function createFinalUpdateZip( $updateZipFile, $classFile, $filesZip )
    {
        $zip = new ZipArchive();

        if ( TRUE === $zip->open( $this->_tmpDir . $updateZipFile, ZipArchive::CREATE ) )
        {
            $info = new SplFileInfo( $classFile );
            $zip->addFile( $classFile, str_replace( $this->_tmpDir, '', $info->getPathname() ) );

            $info = new SplFileInfo( $filesZip );
            $zip->addFile( $filesZip, str_replace( $this->_tmpDir, '', $info->getPathname() ) );

            $zip->close();

            copy( $this->_tmpDir . $updateZipFile, $this->config[ 'storage' ][ 'destination' ][ 'update-dir' ] . DIRECTORY_SEPARATOR . $updateZipFile );

            $this->_cleanupFiles[] = $this->_tmpDir . $updateZipFile;

            $this->cleanUp();

            return TRUE;
        }

        return FALSE;
    }
}