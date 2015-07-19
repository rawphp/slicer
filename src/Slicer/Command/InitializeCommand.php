<?php

namespace Slicer\Command;

use Slicer\Contract\IInstallationManager;
use Slicer\Installer\InteractiveFileBuilder;
use Slicer\Installer\NonInteractiveFileBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

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
                $output->writeln( '<comment>Initialization cancelled</comment>' );

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

        if ( TRUE === $result )
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