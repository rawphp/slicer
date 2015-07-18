<?php

namespace Slicer\Updater;

use Slicer\Contract\IUpdate;
use Slicer\Contract\IUpdateManager;
use Slicer\Manager;
use Slicer\Provider\Contract\IChangeProvider;

/**
 * Class UpdateManager
 *
 * @package Slicer\Updater
 */
class UpdateManager extends Manager implements IUpdateManager
{
    /** @var  IChangeProvider */
    protected $changeProvider;

    /**
     * Set the change provider.
     *
     * @param IChangeProvider $provider
     *
     * @return IUpdateManager
     */
    public function setProvider( IChangeProvider $provider )
    {
        $this->changeProvider = $provider;

        return $this;
    }

    /**
     * Get the change provider.
     *
     * @return IChangeProvider
     */
    public function getProvider()
    {
        return $this->changeProvider;
    }

    /**
     * Create the update.
     *
     * @return IUpdate
     */
    public function createUpdate()
    {
        // TODO: Implement createUpdate() method.
    }

    /**
     * Run an update.
     *
     * @param IUpdate $update
     *
     * @return bool
     */
    public function update( IUpdate $update )
    {
        if ( NULL === $update ) return FALSE;

    }

    /**
     * Rollback an update.
     *
     * @param IUpdate $update
     *
     * @return bool
     */
    public function rollback( IUpdate $update )
    {
        if ( NULL === $update ) return;


    }

    /**
     * Get list of applied updates.
     *
     * @return IUpdate[]
     */
    public function getUpdateHistory()
    {

    }

    /**
     * Get configuration array.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}