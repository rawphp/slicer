<?php
namespace Slicer\Contract;

use Slicer\Config;
use Slicer\Manager;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class InstallationManager
 *
 * @package Slicer\Installer
 */
interface IInstallationManager
{
    /**
     * Check whether Slicer has been initialized for the project.
     *
     * @return bool
     */
    public function checkInstall();

    /**
     * Install Slicer into the project.
     *
     * @return bool
     */
    public function install();

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

    /**
     * Get event dispatcher.
     *
     * @return EventDispatcher
     */
    public function getEventDispatcher();

    /**
     * Set event dispatcher.
     *
     * @param EventDispatcher $event
     *
     * @return Manager
     */
    public function setEventDispatcher( EventDispatcher $event );

    /**
     * Get file builder.
     *
     * @return ISlicerFileBuilder
     */
    public function getFileBuilder();

    /**
     * Set file builder.
     *
     * @param ISlicerFileBuilder $fileBuilder
     *
     * @return IInstallationManager
     */
    public function setFileBuilder( ISlicerFileBuilder $fileBuilder );
}