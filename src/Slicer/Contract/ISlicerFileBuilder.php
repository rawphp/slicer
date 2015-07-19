<?php

namespace Slicer\Contract;

/**
 * Interface ISlicerFileBuilder
 *
 * @package Slicer\Contract
 */
interface ISlicerFileBuilder
{
    /**
     * Construct a default slicer file.
     *
     * @return string
     */
    public function buildFile();
}