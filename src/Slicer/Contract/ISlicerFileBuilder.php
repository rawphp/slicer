<?php

namespace Slicer\Contract;

use Symfony\Component\Console\Output\OutputInterface;

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