<?php

namespace Slicer;

use Slicer\Downloader\DownloadManager;
use Slicer\Installer\InstallationManager;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Slicer
 *
 * @package RawPHP\Slicer
 */
class Slicer
{
    const VERSION = '@package_version@';
    const BRANCH_ALIAS_VERSION = '@package_branch_alias_version@';
    const RELEASE_DATE = '@release_date@';

    /** @var  DownloadManager */
    protected $downloadManager;
    /** @var  InstallationManager */
    protected $installationManager;
    /** @var  array */
    protected $config;
    /** @var  EventDispatcher */
    protected $eventDispatcher;

    /**
     * @return DownloadManager
     */
    public function getDownloadManager()
    {
        return $this->downloadManager;
    }

    /**
     * @param DownloadManager $downloadManager
     */
    public function setDownloadManager( $downloadManager )
    {
        $this->downloadManager = $downloadManager;
    }

    /**
     * @return InstallationManager
     */
    public function getInstallationManager()
    {
        return $this->installationManager;
    }

    /**
     * @param InstallationManager $installationManager
     */
    public function setInstallationManager( $installationManager )
    {
        $this->installationManager = $installationManager;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig( $config )
    {
        $this->config = $config;
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function setEventDispatcher( $eventDispatcher )
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}