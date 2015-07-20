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

namespace Slicer\Provider\Contract;

/**
 * Interface IChangeProvider
 *
 * @package Slicer\Contract\Provider
 */
interface IChangeProvider
{
    /**
     * Get a collection of changed files.
     *  keys =
     *      - added
     *      - modified
     *      - deleted
     *
     * @param string $baseDir base directory
     * @param string $from    from hash
     * @param string $to      to hash
     *
     * @return array
     */
    public function getChangedFiles( $baseDir, $from, $to );
}