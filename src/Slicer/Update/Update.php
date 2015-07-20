<?php

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