<?php

namespace Slicer\Downloader;

use GuzzleHttp\Client;
use Slicer\Contract\IDownloadManager;
use Slicer\Contract\IUpdate;
use Slicer\Manager;

/**
 * Class DownloadManager
 *
 * @package Slicer\Downloader
 */
class DownloadManager extends Manager implements IDownloadManager
{
    /**
     * Upload update.
     *
     * @param IUpdate $update
     *
     * @return bool
     */
    public function uploadUpdate( IUpdate $update )
    {
        // TODO: Implement uploadUpdate() method.
    }

    /**
     * Download updates from the server.
     *
     * @return IUpdate[]
     */
    public function downloadUpdates()
    {
        // TODO: Implement downloadUpdates() method.
    }

    /**
     * Check Slicer version.
     *
     * TRUE if have the latest version, otherwise get version string.
     *
     * @param string $version
     *
     * @return bool|string
     */
    public function checkVersion( $version )
    {
        // TODO: Implement checkVersion() method.
    }

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
    public function downloadVersion( $version = NULL )
    {
        // TODO: Implement downloadVersion() method.
    }

    /**
     * Get a new instance of a Http Client.
     *
     * @param array $options
     *
     * @return Client
     */
    protected function getClient( array $options = [ ] )
    {
        return new Client( $options );
    }
}