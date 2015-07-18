<?php

namespace Slicer\Contract;

use Slicer\Provider\Contract\IChangeProvider;

/**
 * Interface IUpdateManager
 *
 * @package Slicer\Contract
 */
interface IUpdateManager extends IManager
{
    /**
     * Set the change provider.
     *
     * @param IChangeProvider $provider
     *
     * @return IUpdateManager
     */
    public function setProvider( IChangeProvider $provider );

    /**
     * Get the change provider.
     *
     * @return IChangeProvider
     */
    public function getProvider();

    /**
     * Create the update.
     *
     * @return IUpdate
     */
    public function createUpdate();

    /**
     * Run an update.
     *
     * @param IUpdate $update
     *
     * @return bool
     */
    public function update( IUpdate $update );

    /**
     * Rollback an update.
     *
     * @param IUpdate $update
     *
     * @return bool
     */
    public function rollback( IUpdate $update );

    /**
     * Get list of applied updates.
     *
     * @return IUpdate[]
     */
    public function getUpdateHistory();
}