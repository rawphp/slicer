<?php

namespace Slicer\Contract;

use Slicer\Config;

/**
 * Interface IManager
 *
 * @package Slicer\Contract
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
}