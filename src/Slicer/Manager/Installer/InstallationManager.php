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

namespace Slicer\Manager\Installer;

use Exception;
use InvalidArgumentException;
use Slicer\Contract\ISlicerFileBuilder;
use Slicer\Event\PostInstallEvent;
use Slicer\Event\PreInstallEvent;
use Slicer\Manager\Contract\IInstallationManager;
use Slicer\Manager\Manager;

/**
 * Class InstallationManager
 *
 * @package Slicer\Manager\Installer
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

        $res = FALSE;

        try
        {
            $event = new PreInstallEvent( $this->filename, $this->fileBuilder );
            $this->event->dispatch( PreInstallEvent::class, $event );

            $this->filename    = $event->getFilename();
            $this->fileBuilder = $event->getFileBuilder();

            $result = $this->fileBuilder->buildFile();

            if ( '' !== $result )
            {
                file_put_contents( base_path( $this->filename ), $result );

                $res = TRUE;
            }
            else
            {
                $res = 0;
            }
        }
        catch ( Exception $e )
        {
            $res = FALSE;
        }

        $event = new PostInstallEvent( $this->filename, $this->fileBuilder, $res );
        $this->event->dispatch( PostInstallEvent::class, $event );

        return $event->getResult();
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