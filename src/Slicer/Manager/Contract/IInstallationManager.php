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
use Slicer\Contract\ISlicerFileBuilder;
use Slicer\Manager;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class InstallationManager
 *
 * @package Slicer\Manager\Contract
 */
interface IInstallationManager
{
    /**
     * Check whether Slicer has been initialized for the project.
     *
     * @return bool
     */
    public function checkInstall();

    /**
     * Install Slicer into the project.
     *
     * @return bool
     */
    public function install();

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

    /**
     * Get file builder.
     *
     * @return ISlicerFileBuilder
     */
    public function getFileBuilder();

    /**
     * Set file builder.
     *
     * @param ISlicerFileBuilder $fileBuilder
     *
     * @return IInstallationManager
     */
    public function setFileBuilder( ISlicerFileBuilder $fileBuilder );
}