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

namespace Slicer\Event;

use Slicer\Contract\ISlicerFileBuilder;

/**
 * Class PreInstallEvent
 *
 * @package Slicer\Event
 */
class PreInstallEvent extends Event
{
    /** @var  string */
    protected $filename;
    /** @var  ISlicerFileBuilder */
    protected $fileBuilder;

    /**
     * Create new event.
     *
     * @param string             $filename
     * @param ISlicerFileBuilder $builder
     */
    public function __construct( $filename, ISlicerFileBuilder $builder )
    {
        $this->filename    = $filename;
        $this->fileBuilder = $builder;
    }

    /**
     * Get file name.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set file name.
     *
     * @param string $filename
     *
     * @return PreInstallEvent
     */
    public function setFilename( $filename )
    {
        $this->filename = $filename;

        return $this;
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
     * @return PreInstallEvent
     */
    public function setFileBuilder( ISlicerFileBuilder $fileBuilder )
    {
        $this->fileBuilder = $fileBuilder;

        return $this;
    }
}