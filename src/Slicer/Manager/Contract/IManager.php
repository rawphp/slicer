<?php

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