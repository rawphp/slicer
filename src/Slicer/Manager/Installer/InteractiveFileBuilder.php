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

namespace Slicer\Manager\Installer;

use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Class InteractiveFileBuilder
 *
 * @package Slicer\Manager\Installer
 */
class InteractiveFileBuilder extends NonInteractiveFileBuilder
{
    /** @var  InputInterface */
    protected $input;
    /** @var  OutputInterface */
    protected $output;
    /** @var  QuestionHelper */
    protected $helper;

    /**
     * Create interactive file builder.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param HelperInterface $helper
     */
    public function __construct( InputInterface $input, OutputInterface $output, HelperInterface $helper )
    {
        $this->input  = $input;
        $this->output = $output;
        $this->helper = $helper;
    }

    /**
     * Construct a default slicer file.
     *
     * @return string
     */
    public function buildFile()
    {
        $data = [ ];

        /** @var Question $question */
        foreach ( $this->getQuestions() as $key => $question )
        {
            /** @var QuestionHelper $helper */
            $result = $this->helper->ask( $this->input, $this->output, $question );

            $data[ $key ] = $result;
        }

        $data = $this->constructFileStructure( $data );

        $file = print_r( json_encode( $data, JSON_PRETTY_PRINT ), TRUE );

        echo $file . PHP_EOL . PHP_EOL;

        $confirm = new ConfirmationQuestion( PHP_EOL . '<question>How does this look? Continue creating the file?</question> [<comment>yes</comment>] ', TRUE );

        if ( TRUE === $this->helper->ask( $this->input, $this->output, $confirm ) )
        {
            return $file;
        }

        return '';
    }

    /**
     * Get list of questions.
     *
     * @return Question[]
     */
    public function getQuestions()
    {
        return
            [
                'app-name'         => new Question( 'Enter the name of this project: [<comment>slicer/slicer</comment>]', 'slicer/slicer' ),
                'app-description'  => new Question( 'Enter the description: []', '' ),
                'app-key'          => new Question( 'Enter the Application Key: []', '' ),
                'app-secret'       => new Question( 'Enter the Application Secret: []', '' ),
                'backup-files'     => new ConfirmationQuestion( 'Do you want to backup all files BEFORE running updates? [<comment>yes</comment>]', TRUE ),
                'backup-database'  => new ConfirmationQuestion( 'Do you want to backup database BEFORE running updates?  [<comment>yes</comment>]', TRUE ),
                'update-class'     => new Question( 'What Update class do you want to use as base? [<comment>Slicer\\Update</comment>]', 'Slicer\\Update' ),
                'update-namespace' => new Question( 'What Update namespace do you want to use? [<comment>Slicer\\Update</comment>]', 'Slicer\\Update' ),
                'change-provider'  => new ChoiceQuestion( 'What change provider do you want to use?  [<comment>Git</comment>]', [ 'Git', 'Null' ], 'Git' ),
                'private-key'      => new Question( 'Provide the path to your private signing key: []', '' ),
                'public-key'       => new Question( 'Provide the path to your public signing key: []', '' ),
                'backup-dir'       => new Question( 'Provide the path to the backup directory: [<comment>slicer/backup</comment>]', 'slicer/backup' ),
                'backup-type'      => new ChoiceQuestion( 'What type of backup file do you want to use? [<comment>single</comment>]', [ 'single', 'unique' ], 'single' ),
                'backup-ignore'    => new Question( 'Provide a comma-delimited list of directories to ignore during backups: []', '' ),

            ];
    }

    /**
     * Get change provider by driver key.
     *
     * @param string $key
     *
     * @return string
     */
    public function getChangeProviderClass( $key )
    {
        $providers =
            [
                'Git' => 'Slicer\\Provider\\GitProvider',
            ];

        return $providers[ $key ];
    }

    /**
     * Construct file structure.
     *
     * @param array $data
     *
     * @return array
     */
    public function constructFileStructure( $data = [ ] )
    {
        $file = parent::constructFileStructure( $data );

        $file =
            array_replace_recursive(
                $file,
                [
                    'app'             =>
                        [
                            'name'        => $data[ 'app-name' ],
                            'description' => $data[ 'app-description' ],
                            'app-key'     => $data[ 'app-key' ],
                            'app-secret'  => $data[ 'app-secret' ],
                        ],
                    'options'         =>
                        [
                            'update' =>
                                [
                                    'backup-files'    => $data[ 'backup-files' ],
                                    'backup-database' => $data[ 'backup-database' ],
                                ],
                        ],
                    'update-file'     =>
                        [
                            'class'     => $data[ 'update-class' ],
                            'namespace' => $data[ 'update-namespace' ],
                        ],
                    'change-provider' =>
                        [
                            'driver' => $data[ 'change-provider' ],
                            'class'  => $this->getChangeProviderClass( $data[ 'change-provider' ] ),
                        ],
                    'signing'         =>
                        [
                            'private-key' => $data[ 'private-key' ],
                            'public-key'  => $data[ 'public-key' ],
                        ],
                    'storage'         =>
                        [
                            'tmp-dir'    => 'slicer' . DIRECTORY_SEPARATOR . 'tmp',
                            'update-dir' => 'slicer' . DIRECTORY_SEPARATOR . 'updates',
                            'backup-dir' => $data[ 'backup-dir' ],
                        ],
                    'backup'          =>
                        [
                            'exclude-dirs' => explode( ',', $data[ 'backup-ignore' ] ),
                            'file-type'    => $data[ 'backup-type' ],
                        ],
                    'base-dir'        => '',
                ]
            );

        $file[ 'app' ][ 'name' ]            = strtr( $file[ 'app' ][ 'name' ], "/", "/" );
        $file[ 'signing' ][ 'private-key' ] = strtr( $file[ 'signing' ][ 'private-key' ], '\/', '/' );
        $file[ 'signing' ][ 'public-key' ]  = strtr( $file[ 'signing' ][ 'public-key' ], '\/', '/' );

        $dirs = [ ];

        foreach ( $file[ 'backup' ][ 'exclude-dirs' ] as $dir )
        {
            if ( '' !== trim( $dir ) )
            {
                $dirs[] = strtr( $dir, '\/', '/' );
            }
        }

        $file[ 'backup' ][ 'exclude-dirs' ] = $dirs;

        return $file;
    }
}