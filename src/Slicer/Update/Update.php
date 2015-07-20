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

namespace Slicer\Update;

use Slicer\Contract\IUpdate;

/**
 * Class Update
 *
 * @package Slicer\Update
 */
abstract class Update implements IUpdate
{
    /** @var  bool */
    protected $enabled;

    /**
     * Check if update is ready to be applied.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set whether update is enabled.
     *
     * @param bool $enabled
     *
     * @return $this
     */
    public function setIsEnabled( $enabled )
    {
        $this->enabled = $enabled;

        return $this;
    }
}