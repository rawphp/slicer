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

/**
 * Class PostBackupEvent
 *
 * @package Slicer\Event
 */
class PostBackupEvent extends Event
{
    /** @var  array */
    protected $options;
    /** @var  string */
    protected $archiveLocation;

    /**
     * Create event.
     *
     * @param string $archive
     * @param array  $options
     */
    public function __construct( $archive, array $options )
    {
        $this->archive = $archive;
        $this->options = $options;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set options.
     *
     * @param array $options
     *
     * @return Event
     */
    public function setOptions( array $options )
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get archive.
     *
     * @return string
     */
    public function getArchiveLocation()
    {
        return $this->archive;
    }

    /**
     * Set archive location.
     *
     * @param string $archive
     *
     * @return Event
     */
    public function setArchiveLocation( $archive )
    {
        $this->archive = $archive;

        return $this;
    }
}