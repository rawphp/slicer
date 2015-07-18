<?php

namespace Slicer\Contract;

/**
 * Interface IBackupManager
 *
 * @package Slicer\Contract
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
     * @return bool
     */
    public function restore();
}