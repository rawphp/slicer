<?php

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
    protected $updateFile = 'Slicer\\Update';
    /** @var  array */
    protected $changeProvider;
    /** @var  array */
    protected $signing;
    /** @var  array */
    protected $storage;

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
                    $this->updateFile = $value;
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
                    $this->options = array_merge_recursive( $this->options, $value );
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
     * @return string
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * @param string $appName
     */
    public function setAppName( $appName )
    {
        $this->appName = $appName;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription( $description )
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getAppKey()
    {
        return $this->appKey;
    }

    /**
     * @param string $appKey
     */
    public function setAppKey( $appKey )
    {
        $this->appKey = $appKey;
    }

    /**
     * @return string
     */
    public function getAppSecret()
    {
        return $this->appSecret;
    }

    /**
     * @param string $appSecret
     */
    public function setAppSecret( $appSecret )
    {
        $this->appSecret = $appSecret;
    }

    /**
     * @return string
     */
    public function getBaseDir()
    {
        return $this->baseDir;
    }

    /**
     * @param string $baseDir
     */
    public function setBaseDir( $baseDir )
    {
        $this->baseDir = $baseDir;
    }

    /**
     * @return string
     */
    public function getSlicerDir()
    {
        return $this->slicerDir;
    }

    /**
     * @param string $slicerDir
     */
    public function setSlicerDir( $slicerDir )
    {
        $this->slicerDir = $slicerDir;
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * @param string $cacheDir
     */
    public function setCacheDir( $cacheDir )
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions( $options )
    {
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getUpdateFile()
    {
        return $this->updateFile;
    }

    /**
     * @param string $updateFile
     */
    public function setUpdateFile( $updateFile )
    {
        $this->updateFile = $updateFile;
    }

    /**
     * @return array
     */
    public function getChangeProvider()
    {
        return $this->changeProvider;
    }

    /**
     * @param array $changeProvider
     */
    public function setChangeProvider( $changeProvider )
    {
        $this->changeProvider = $changeProvider;
    }

    /**
     * @return array
     */
    public function getSigning()
    {
        return $this->signing;
    }

    /**
     * @param array $signing
     */
    public function setSigning( $signing )
    {
        $this->signing = $signing;
    }

    /**
     * @return array
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param array $storage
     */
    public function setStorage( $storage )
    {
        $this->storage = $storage;
    }
}