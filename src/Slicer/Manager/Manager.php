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

namespace Slicer\Manager;

use Slicer\Config;
use Slicer\Manager\Contract\IManager;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Manager
 *
 * @package Slicer\Manager
 */
abstract class Manager implements IManager
{
    /** @var  array */
    protected $config;
    /** @var  EventDispatcher */
    protected $event;

    /**
     * Create new manager.
     *
     * @param Config $config
     */
    public function __construct( Config $config )
    {
        $this->config = $config;
    }

    /**
     * Get config.
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set config.
     *
     * @param Config $config
     *
     * @return Manager
     */
    public function setConfig( Config $config )
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get event dispatcher.
     *
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->event;
    }

    /**
     * Set event dispatcher.
     *
     * @param EventDispatcher $event
     *
     * @return Manager
     */
    public function setEventDispatcher( EventDispatcher $event )
    {
        $this->event = $event;

        return $this;
    }
}