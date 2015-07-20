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

use InvalidArgumentException;
use Slicer\Console\Application;
use Slicer\Manager\Contract\IUpdateManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateCommand
 *
 * @package Slicer\Command
 */
class CreateCommand extends Command
{
    /** @var IUpdateManager */
    protected $updateManager;

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName( 'create' )
            ->setDescription( 'Create a new update' )
            ->addArgument(
                'starting-version',
                InputArgument::REQUIRED,
                'The name of the starting version',
                NULL
            )
            ->addArgument(
                'ending-version',
                InputArgument::REQUIRED,
                'The name of the ending version',
                NULL
            )
            ->addArgument(
                'provider',
                InputArgument::OPTIONAL,
                'The change file provider to use',
                NULL
            );
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        /** @var Application $app */
        $app                 = $this->getApplication();
        $this->updateManager = $app->getSlicer()->getUpdateManager();

        if ( NULL === $this->updateManager->getChangeProvider() && !$input->hasArgument( 'provider' ) )
        {
            throw new InvalidArgumentException( 'Change Provider must be specified either in slicer.json or as an argument' );
        }

        if ( $input->hasArgument( 'provider' ) )
        {
            $provider = $input->getArgument( 'provider' );
            die( $provider );
        }

        $start = $input->getArgument( 'starting-version' );
        $end   = $input->getArgument( 'ending-version' );

        $result = $this->updateManager->createUpdate( $start, $end );


    }
}