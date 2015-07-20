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