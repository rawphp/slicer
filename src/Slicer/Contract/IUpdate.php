<?php

namespace Slicer\Contract;

use DateTime;

/**
 * Interface IUpdate
 *
 * @package Slicer\Contract
 */
interface IUpdate
{
    /**
     * Check if update is ready to be applied.
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Get date update was run.
     *
     * @return DateTime
     */
    public function getDateUpdateApplied();

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