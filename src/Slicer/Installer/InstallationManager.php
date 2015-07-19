<?php

namespace Slicer\Installer;

use Exception;
use InvalidArgumentException;
use Slicer\Contract\IInstallationManager;
use Slicer\Contract\ISlicerFileBuilder;
use Slicer\Manager;

/**
 * Class InstallationManager
 *
 * @package Slicer\Installer
 */
class InstallationManager extends Manager implements IInstallationManager
{
    /** @var  ISlicerFileBuilder */
    protected $fileBuilder;
    /** @var  string */
    protected $filename = 'slicer.json';

    /**
     * Check whether Slicer has been initialized for the project.
     *
     * @return bool
     */
    public function checkInstall()
    {
        return file_exists( base_path( $this->filename ) );
    }

    /**
     * Install Slicer into the project.
     *
     * @return bool
     */
    public function install()
    {
        if ( NULL === $this->fileBuilder )
        {
            throw new InvalidArgumentException( 'Slicer\Contract\ISlicerFileBuilder must be set before running install()' );
        }
        try
        {
            $result = $this->fileBuilder->buildFile();

            if ( '' !== $result )
            {
                file_put_contents( base_path( $this->filename ), $result );

                return TRUE;
            }

            return FALSE;
        }
        catch ( Exception $e )
        {
            return FALSE;
        }
    }

    /**
     * Get file builder.
     *
     * @return ISlicerFileBuilder
     */
    public function getFileBuilder()
    {
        return $this->fileBuilder;
    }

    /**
     * Set file builder.
     *
     * @param ISlicerFileBuilder $fileBuilder
     *
     * @return IInstallationManager
     */
    public function setFileBuilder( ISlicerFileBuilder $fileBuilder )
    {
        $this->fileBuilder = $fileBuilder;

        return $this;
    }

    /**
     * Get slicer file name.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set slicer file name.
     *
     * @param string $filename
     */
    public function setFilename( $filename )
    {
        $this->filename = $filename;
    }
}