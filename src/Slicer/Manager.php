<?php

namespace Slicer;

/**
 * Class Manager
 *
 * @package Slicer
 */
abstract class Manager
{
    /** @var  array */
    protected $config;

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
}