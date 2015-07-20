<?php

/**
 * This file is part of Slicer.
 *
 * Copyright (c) 2015 Tom Kaczocha <tom@rawphp.org>
 *
 * This Source Code is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, you can obtain one at http://mozilla.org/MPL/2.0/.
 *
 * PHP version 5.6
 */

namespace Slicer;

use Slicer\Manager\Contract\IBackupManager;
use Slicer\Manager\Contract\IDownloadManager;
use Slicer\Manager\Contract\IInstallationManager;
use Slicer\Manager\Contract\IUpdateManager;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Slicer
 *
 * @package Slicer
 */
class Slicer
{
    const VERSION = '@package_version@';
    const BRANCH_ALIAS_VERSION = '@package_branch_alias_version@';
    const RELEASE_DATE = '@release_date@';

    /** @var  IDownloadManager */
    protected $downloadManager;
    /** @var  IInstallationManager */
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
     * @return IDownloadManager
     */
    public function getDownloadManager()
    {
        return $this->downloadManager;
    }

    /**
     * @param IDownloadManager $downloadManager
     *
     * @return Slicer
     */
    public function setDownloadManager( IDownloadManager $downloadManager )
    {
        $this->downloadManager = $downloadManager;

        return $this;
    }

    /**
     * @return IInstallationManager
     */
    public function getInstallationManager()
    {
        return $this->installationManager;
    }

    /**
     * @param IInstallationManager $installationManager
     *
     * @return Slicer
     */
    public function setInstallationManager( IInstallationManager $installationManager )
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