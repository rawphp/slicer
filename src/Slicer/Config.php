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

/**
 * Class Config
 *
 * @package Slicer
 */
class Config
{
    /** @var  string */
    protected $appName;
    /** @var  string */
    protected $description;
    /** @var  string */
    protected $appKey;
    /** @var  string */
    protected $appSecret;
    /** @var  string */
    protected $baseDir;
    /** @var  string */
    protected $slicerDir = 'slicer';
    /** @var  string */
    protected $cacheDir = '{$home}/cache';
    /** @var  array */
    protected $options = [ ];
    /** @var  string */
    protected $updateFile = 'Slicer\\Update\\Update';
    /** @var  string */
    protected $updateNamespace = 'Slicer\\Update';
    /** @var  array */
    protected $changeProvider;
    /** @var  array */
    protected $signing;
    /** @var  array */
    protected $storage;
    /** @var  array */
    protected $backup;

    /**
     * Create a new config.
     *
     * @param array $config
     */
    public function __construct( array $config )
    {
        $this->init( $config );
    }

    /**
     * Initialise config.
     *
     * @param array $config
     */
    protected function init( array $config )
    {
        if ( empty( $config ) ) return;

        foreach ( $config as $key => $value )
        {
            switch ( $key )
            {
                case 'base_dir':
                    $this->baseDir = $value;
                    break;
                case 'cache_dir':
                    $this->cacheDir = $value;
                    break;
                case 'app':
                    foreach ( $value as $k => $v )
                    {
                        switch ( $k )
                        {
                            case 'name':
                                $this->appName = $v;
                                break;
                            case 'description':
                                $this->description = $v;
                                break;
                            case 'app_key':
                                $this->appKey = $v;
                                break;
                            case 'app_secret':
                                $this->appSecret = $v;
                                break;
                        }
                    }
                    break;
                case 'options':
                    $this->options = array_merge_recursive( $this->options, $value );
                    break;
                case 'update_file':
                    $this->updateFile      = $value[ 'class' ];
                    $this->updateNamespace = $value[ 'namespace' ];
                    break;
                case 'change_provider':
                    $this->changeProvider = $value;
                    break;
                case 'signing':
                    $this->signing = $value;
                    break;
                case 'storage':
                    $this->storage = $value;
                    break;
                case 'backup':
                    $this->backup = $value;
                    break;
            }
        }
    }

    /**
     * Merges new config values with the existing ones (overriding).
     *
     * @param array $config
     */
    public function merge( $config )
    {
        $this->init( $config );
    }

    /**
     * Get app name.
     *
     * @return string
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * Set app name.
     *
     * @param string $appName
     *
     * @return Config
     */
    public function setAppName( $appName )
    {
        $this->appName = $appName;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Config
     */
    public function setDescription( $description )
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get app key.
     *
     * @return string
     */
    public function getAppKey()
    {
        return $this->appKey;
    }

    /**
     * Set app key.
     *
     * @param string $appKey
     *
     * @return Config
     */
    public function setAppKey( $appKey )
    {
        $this->appKey = $appKey;

        return $this;
    }

    /**
     * Get app secret.
     *
     * @return string
     */
    public function getAppSecret()
    {
        return $this->appSecret;
    }

    /**
     * Set app secret.
     *
     * @param string $appSecret
     *
     * @return Config
     */
    public function setAppSecret( $appSecret )
    {
        $this->appSecret = $appSecret;

        return $this;
    }

    /**
     * Get base directory.
     *
     * @return string
     */
    public function getBaseDir()
    {
        return $this->baseDir;
    }

    /**
     * Set base directory.
     *
     * @param string $baseDir
     *
     * @return Config
     */
    public function setBaseDir( $baseDir )
    {
        $this->baseDir = $baseDir;

        return $this;
    }

    /**
     * Get slicer directory.
     *
     * @return string
     */
    public function getSlicerDir()
    {
        return $this->slicerDir;
    }

    /**
     * Set slicer directory.
     *
     * @param string $slicerDir
     *
     * @return Config
     */
    public function setSlicerDir( $slicerDir )
    {
        $this->slicerDir = $slicerDir;

        return $this;
    }

    /**
     * Get cache directory.
     *
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * Set cache directory.
     *
     * @param string $cacheDir
     *
     * @return Config
     */
    public function setCacheDir( $cacheDir )
    {
        $this->cacheDir = $cacheDir;

        return $this;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set options.
     *
     * @param array $options
     *
     * @return Config
     */
    public function setOptions( array $options )
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get update file.
     *
     * @return string
     */
    public function getUpdateFile()
    {
        return $this->updateFile;
    }

    /**
     * Set update file.
     *
     * @param string $updateFile
     *
     * @return Config
     */
    public function setUpdateFile( $updateFile )
    {
        $this->updateFile = $updateFile;

        return $this;
    }

    /**
     * Get update namespace.
     *
     * @return string
     */
    public function getUpdateNamespace()
    {
        return $this->updateNamespace;
    }

    /**
     * Set update namespace.
     *
     * @param $namespace
     *
     * @return Config
     */
    public function setUpdateNamespace( $namespace )
    {
        $this->updateNamespace = $namespace;

        return $this;
    }

    /**
     * Get signing.
     *
     * @return array
     */
    public function getSigning()
    {
        return $this->signing;
    }

    /**
     * Set signing.
     *
     * @param array $signing
     *
     * @return Config
     */
    public function setSigning( array $signing )
    {
        $this->signing = $signing;

        return $this;
    }

    /**
     * Get storage.
     *
     * @return array
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set storage.
     *
     * @param array $storage
     *
     * @return Config
     */
    public function setStorage( array $storage )
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * Get backup.
     *
     * @return array
     */
    public function getBackup()
    {
        return $this->backup;
    }

    /**
     * Set backup.
     *
     * @param array $backup
     */
    public function setBackup( array $backup )
    {
        $this->backup = $backup;
    }

    /**
     * Get change provider settings.
     *
     * @return array
     */
    public function getChangeProvider()
    {
        return $this->changeProvider;
    }

    /**
     * Set change provider settings.
     *
     * @param array $changeProvider
     */
    public function setChangeProvider( array $changeProvider )
    {
        $this->changeProvider = $changeProvider;
    }
}