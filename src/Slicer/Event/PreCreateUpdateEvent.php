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
 * Class PreCreateUpdateEvent
 *
 * @package Slicer\Event
 */
class PreCreateUpdateEvent extends Event
{
    /** @var  string */
    protected $startHash;
    /** @var  string */
    protected $endHash;

    /**
     * Create new event.
     *
     * @param string $start
     * @param string $end
     */
    public function __construct( $start, $end )
    {
        $this->startHash = $start;
        $this->endHash   = $end;
    }

    /**
     * Get start hash.
     *
     * @return string
     */
    public function getStartHash()
    {
        return $this->startHash;
    }

    /**
     * Set start hash.
     *
     * @param string $startHash
     *
     * @return PreCreateUpdateEvent
     */
    public function setStartHash( $startHash )
    {
        $this->startHash = $startHash;

        return $this;
    }

    /**
     * Get end hash.
     *
     * @return string
     */
    public function getEndHash()
    {
        return $this->endHash;
    }

    /**
     * Set end hash.
     *
     * @param string $endHash
     *
     * @return PreCreateUpdateEvent
     */
    public function setEndHash( $endHash )
    {
        $this->endHash = $endHash;

        return $this;
    }

}