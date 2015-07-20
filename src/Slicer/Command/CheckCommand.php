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

namespace Slicer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckCommand
 *
 * @package Slicer\Command
 */
class CheckCommand extends Command
{
    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName( 'check' )
            ->setDescription( 'Check for available updates on the server' )
            ->addArgument(
                'app-key',
                InputArgument::REQUIRED,
                'The application key',
                NULL
            )
            ->addArgument(
                'app-secret',
                InputArgument::REQUIRED,
                'The application secret',
                NULL
            );
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute( InputInterface $input, OutputInterface $output )
    {
    }
}