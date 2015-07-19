<?php

namespace Slicer\Event;

/**
 * Class PreCreateUpdateEvent
 *
 * @package Slicer\Event
 */
class PreCreateUpdateEvent extends Event
{
    /** @var  string */
    protected $startHash;
    /** @var  string */
    protected $endHash;

    /**
     * Create new event.
     *
     * @param string $start
     * @param string $end
     */
    public function __construct( $start, $end )
    {
        $this->startHash = $start;
        $this->endHash   = $end;
    }

    /**
     * Get start hash.
     *
     * @return string
     */
    public function getStartHash()
    {
        return $this->startHash;
    }

    /**
     * Set start hash.
     *
     * @param string $startHash
     *
     * @return PreCreateUpdateEvent
     */
    public function setStartHash( $startHash )
    {
        $this->startHash = $startHash;

        return $this;
    }

    /**
     * Get end hash.
     *
     * @return string
     */
    public function getEndHash()
    {
        return $this->endHash;
    }

    /**
     * Set end hash.
     *
     * @param string $endHash
     *
     * @return PreCreateUpdateEvent
     */
    public function setEndHash( $endHash )
    {
        $this->endHash = $endHash;

        return $this;
    }

}