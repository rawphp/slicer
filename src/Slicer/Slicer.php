<?php

namespace Slicer;

use Slicer\Contract\IBackupManager;
use Slicer\Contract\IUpdateManager;
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
    /** @var  Config */
    protected $config;
    /** @var  EventDispatcher */
    protected $eventDispatcher;
    /** @var  IUpdateManager */
    protected $updateManager;
    /** @var  IBackupManager */
    protected $backupManager;

    /**
     * @return DownloadManager
     */
    public function getDownloadManager()
    {
        return $this->downloadManager;
    }

    /**
     * @param DownloadManager $downloadManager
     *
     * @return Slicer
     */
    public function setDownloadManager( DownloadManager $downloadManager )
    {
        $this->downloadManager = $downloadManager;

        return $this;
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
     *
     * @return Slicer
     */
    public function setInstallationManager( InstallationManager $installationManager )
    {
        $this->installationManager = $installationManager;

        return $this;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param Config $config
     *
     * @return Slicer
     */
    public function setConfig( Config $config )
    {
        $this->config = $config;

        return $this;
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
     *
     * @return Slicer
     */
    public function setEventDispatcher( EventDispatcher $eventDispatcher )
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * @return IUpdateManager
     */
    public function getUpdateManager()
    {
        return $this->updateManager;
    }

    /**
     * @param IUpdateManager $updateManager
     *
     * @return Slicer
     */
    public function setUpdateManager( IUpdateManager $updateManager )
    {
        $this->updateManager = $updateManager;

        return $this;
    }

    /**
     * @return IBackupManager
     */
    public function getBackupManager()
    {
        return $this->backupManager;
    }

    /**
     * @param IBackupManager $backupManager
     *
     * @return Slicer
     */
    public function setBackupManager( IBackupManager $backupManager )
    {
        $this->backupManager = $backupManager;

        return $this;
    }
}