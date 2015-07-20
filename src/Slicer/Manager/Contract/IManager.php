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

namespace Slicer\Manager\Contract;

use Slicer\Config;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Interface IManager
 *
 * @package Slicer\Manager\Contract
 */
interface IManager
{
    /**
     * Get config.
     *
     * @return Config
     */
    public function getConfig();

    /**
     * Set config.
     *
     * @param Config $config
     *
     * @return Manager
     */
    public function setConfig( Config $config );

    /**
     * Get event dispatcher.
     *
     * @return EventDispatcher
     */
    public function getEventDispatcher();

    /**
     * Set event dispatcher.
     *
     * @param EventDispatcher $event
     *
     * @return Manager
     */
    public function setEventDispatcher( EventDispatcher $event );
}