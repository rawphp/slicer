<?php

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