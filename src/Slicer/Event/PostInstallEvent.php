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
 * Class PostInstallEvent
 *
 * @package Slicer\Event
 */
class PostInstallEvent extends Event
{
    /** @var  string */
    protected $filename;
    /** @var  ISlicerFileBuilder */
    protected $fileBuilder;
    /** @var  bool */
    protected $result;

    /**
     * Create new event.
     *
     * @param string             $filename
     * @param ISlicerFileBuilder $builder
     * @param bool               $result
     */
    public function __construct( $filename, ISlicerFileBuilder $builder, $result )
    {
        $this->filename    = $filename;
        $this->fileBuilder = $builder;
        $this->result      = $result;
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
     * Get file builder.
     *
     * @return ISlicerFileBuilder
     */
    public function getFileBuilder()
    {
        return $this->fileBuilder;
    }

    /**
     * Get result.
     *
     * @return boolean
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set result.
     *
     * @param boolean $result
     *
     * @return PostInstallEvent
     */
    public function setResult( $result )
    {
        $this->result = ( bool ) $result;

        return $this;
    }
}