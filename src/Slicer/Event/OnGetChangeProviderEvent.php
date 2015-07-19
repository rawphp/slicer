<?php

namespace Slicer\Event;

use Slicer\Provider\Contract\IChangeProvider;

/**
 * Class OnGetChangeProviderEvent
 *
 * @package Slicer\Event
 */
class OnGetChangeProviderEvent extends Event
{
    /** @var  IChangeProvider */
    protected $changeProvider;

    /**
     * Create new event.
     *
     * @param IChangeProvider $provider
     */
    public function __construct( IChangeProvider $provider )
    {
        $this->changeProvider = $provider;
    }

    /**
     * Get change provider.
     *
     * @return IChangeProvider
     */
    public function getChangeProvider()
    {
        return $this->changeProvider;
    }

    /**
     * Set change provider.
     *
     * @param IChangeProvider $changeProvider
     *
     * @return OnGetChangeProviderEvent
     */
    public function setChangeProvider( IChangeProvider $changeProvider )
    {
        $this->changeProvider = $changeProvider;

        return $this;
    }
}