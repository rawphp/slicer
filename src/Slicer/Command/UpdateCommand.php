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

use Slicer\Console\Application;
use Slicer\Manager\Contract\IUpdateManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateCommand
 *
 * @package RawPHP\Slicer\Command
 */
class UpdateCommand extends Command
{
    /** @var  IUpdateManager */
    protected $updateManager;

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName( 'update' )
            ->setDescription( 'Run locally available updates' )
            ->addArgument(
                'version',
                InputArgument::OPTIONAL,
                'Upgrade version',
                NULL
            );
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        /** @var Application $app */
        $app                 = $this->getApplication();
        $this->updateManager = $app->getSlicer()->getUpdateManager();

        $this->updateManager->update( NULL );
    }
}