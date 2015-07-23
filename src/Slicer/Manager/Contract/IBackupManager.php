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

namespace Slicer\Manager\Contract;

/**
 * Interface IBackupManager
 *
 * @package Slicer\Manager\Contract
 */
interface IBackupManager extends IManager
{
    /**
     * Create a backup.
     *
     * @param array $options
     *
     * @return bool
     */
    public function backup( array $options );

    /**
     * Restore from backup.
     *
     * @param array $options
     *
     * @return bool
     */
    public function restore( array $options );
}