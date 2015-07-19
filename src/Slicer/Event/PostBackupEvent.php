<?php

namespace Slicer\Event;

/**
 * Class PostBackupEvent
 *
 * @package Slicer\Event
 */
class PostBackupEvent extends Event
{
    /** @var  array */
    protected $options;
    /** @var  string */
    protected $archiveLocation;

    /**
     * Create event.
     *
     * @param string $archive
     * @param array  $options
     */
    public function __construct( $archive, array $options )
    {
        $this->archive = $archive;
        $this->options = $options;
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
     * @return Event
     */
    public function setOptions( array $options )
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get archive.
     *
     * @return string
     */
    public function getArchiveLocation()
    {
        return $this->archive;
    }

    /**
     * Set archive location.
     *
     * @param string $archive
     *
     * @return Event
     */
    public function setArchiveLocation( $archive )
    {
        $this->archive = $archive;

        return $this;
    }
}