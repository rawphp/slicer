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

use Slicer\Manager\Contract\IInstallationManager;
use Slicer\Manager\Installer\InteractiveFileBuilder;
use Slicer\Manager\Installer\NonInteractiveFileBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class InitializeCommand
 *
 * @package Slicer\Command
 */
class InitializeCommand extends Command
{
    /** @var  IInstallationManager */
    protected $installationManager;

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName( 'init' )
            ->setDescription( 'Initialize and generate a default configuration file' )
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'Location for the config file',
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
        $this->installationManager = $this->getApplication()->getSlicer()->getInstallationManager();

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper( 'question' );

        if ( TRUE === $this->installationManager->checkInstall() )
        {
            $question = new ConfirmationQuestion( PHP_EOL . 'slicer.json already exists in the root of the site.' . PHP_EOL . '<question>Are you sure you want to overwrite it and create a new one?</question> ', FALSE );

            if ( FALSE === $helper->ask( $input, $output, $question ) )
            {
                $output->writeln( '<error>Command aborted</error>' );

                return 0;
            }
        }

        if ( $input->getOption( 'no-interaction' ) )
        {
            $this->installationManager->setFileBuilder( new NonInteractiveFileBuilder( $input, $output ) );
        }
        else
        {
            $this->installationManager->setFileBuilder( new InteractiveFileBuilder( $input, $output, $helper ) );
        }

        $result = $this->installationManager->install();

        if ( 0 === $result )
        {
            $output->writeln( '<error>Command aborted</error>' );

            return 0;
        }
        elseif ( TRUE === $result )
        {
            $output->writeln( 'Slicer initialized successfully to <info>' . base_path( 'slicer.json' ) . '</info>' );

            return 0;
        }
        else
        {
            $output->writeln( '<error>Failed to initialize Slicer.</error>' );

            return 1;
        }
    }
}