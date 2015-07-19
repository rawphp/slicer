<?php

namespace Slicer\Manager\Contract;

use Slicer\Contract\IUpdate;
use Slicer\Provider\Contract\IChangeProvider;

/**
 * Interface IUpdateManager
 *
 * @package Slicer\Manager\Contract
 */
interface IUpdateManager extends IManager
{
    /**
     * Create the update.
     *
     * @param string $start
     * @param string $end
     *
     * @return IUpdate
     */
    public function createUpdate( $start, $end );

    /**
     * Upload an update to the server.
     *
     * @return mixed
     */
    public function publishUpdate();

    /**
     * Run an update check.
     *
     * @return bool
     */
    public function updateCheck();

    /**
     * Get available updates from the server.
     *
     * @return mixed
     */
    public function getUpdates();

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

    /**
     * Set the change provider.
     *
     * @param IChangeProvider $provider
     *
     * @return IUpdateManager
     */
    public function setChangeProvider( IChangeProvider $provider );

    /**
     * Get the change provider.
     *
     * @return IChangeProvider
     */
    public function getChangeProvider();

    /**
     * @return IDownloadManager
     */
    public function getDownloadManager();

    /**
     * Set download manager.
     *
     * @param IDownloadManager $downloadManager
     *
     * @return IUpdateManager
     */
    public function setDownloadManager( IDownloadManager $downloadManager );
}