<?php

namespace Slicer\Manager\Contract;

use Slicer\Contract\IUpdate;

/**
 * Interface IDownloadManager
 *
 * @package Slicer\Manager\Contract
 */
interface IDownloadManager extends IManager
{
    /**
     * Upload update.
     *
     * @param IUpdate $update
     *
     * @return bool
     */
    public function uploadUpdate( IUpdate $update );

    /**
     * Download updates from the server.
     *
     * @return IUpdate[]
     */
    public function downloadUpdates();

    /**
     * Check Slicer version.
     *
     * TRUE if have the latest version, otherwise get version string.
     *
     * @param string $version
     *
     * @return bool|string
     */
    public function checkVersion( $version );

    /**
     * Download a version of Slicer.
     *
     * If version is not specified, then download the latest.
     *
     * Return the path to the downloaded file or FALSE on error.
     *
     * @param string $version
     *
     * @return string|bool
     */
    public function downloadVersion( $version = NULL );
}