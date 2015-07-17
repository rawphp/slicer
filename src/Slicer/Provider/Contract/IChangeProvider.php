<?php

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