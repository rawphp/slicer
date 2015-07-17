<?php

namespace Slicer\Contract;

/**
 * Interface IUpdate
 *
 * @package Slicer\Contract
 */
interface IUpdate
{
    /**
     * Copy new and updated files into the project.
     *
     * @return bool
     */
    public function updateFiles( );

    /**
     * Delete old files.
     *
     * @return bool
     */
    public function deleteFiles( );
}