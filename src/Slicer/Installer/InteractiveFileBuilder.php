<?php

namespace Slicer\Installer;

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
 * @package Slicer\Installer
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

        $confirm = new ConfirmationQuestion( '<question>How does this look? Continue creating the file?</question> ', TRUE );

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
                'app-name'        => new Question( 'Enter the name of this project: ', 'slicer/slicer' ),
                'app-description' => new Question( 'Enter the description: ', '' ),
                'app-key'         => new Question( 'Enter the Application Key: ', '' ),
                'app-secret'      => new Question( 'Enter the Application Secret: ', '' ),
                'backup-files'    => new ConfirmationQuestion( 'Do you want to backup all files BEFORE running updates? ', TRUE ),
                'backup-database' => new ConfirmationQuestion( 'Do you want to backup database BEFORE running updates? ', TRUE ),
                'update-class'    => new Question( 'What Update class do you want to use as base? ', 'Slicer\\Update' ),
                'change-provider' => new ChoiceQuestion( 'What change provider do you want to use? ', [ 'Git', 'Null' ], 'Git' ),
                'private-key'     => new Question( 'Provide the path to your private signing key: ', '' ),
                'public-key'      => new Question( 'Provide the path to your public signing key: ', '' ),
                'backup-type'     => new ChoiceQuestion( 'What type of backup file do you want to use? ', [ 'single', 'unique' ], 'single' ),
                'backup-ignore'   => new Question( 'Provide a comma-delimited list of directories to ignore during backups: ', '' ),
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
                            'app_key'     => $data[ 'app-key' ],
                            'app_secret'  => $data[ 'app-secret' ],
                        ],
                    'options'         =>
                        [
                            'update' =>
                                [
                                    'backup-files'    => $data[ 'backup-files' ],
                                    'backup-database' => $data[ 'backup-database' ],
                                ],
                        ],
                    'update_file'     => $data[ 'update-class' ],
                    'change_provider' =>
                        [
                            'driver' => $data[ 'change-provider' ],
                            'class'  => $this->getChangeProviderClass( $data[ 'change-provider' ] ),
                        ],
                    'signing'         =>
                        [
                            'private_key' => $data[ 'private-key' ],
                            'public_key'  => $data[ 'public-key' ],
                        ],
                    'storage'         =>
                        [
                            'source'      =>
                                [
                                    'tmp-dir' => 'slicer' . DIRECTORY_SEPARATOR . 'tmp',
                                ],
                            'destination' =>
                                [
                                    'update-dir' => 'slicer' . DIRECTORY_SEPARATOR . 'updates',
                                ],
                        ],
                    'backup'          =>
                        [
                            'exclude-dirs' => explode( ',', $data[ 'backup-ignore' ] ),
                            'file-type'    => $data[ 'backup-type' ],
                        ],
                    'base_dir'        => '',
                ]
            );

        $file[ 'app' ][ 'name' ]            = strtr( $file[ 'app' ][ 'name' ], "/", "/" );
        $file[ 'signing' ][ 'private_key' ] = strtr( $file[ 'signing' ][ 'private_key' ], '\/', '/' );
        $file[ 'signing' ][ 'public_key' ]  = strtr( $file[ 'signing' ][ 'public_key' ], '\/', '/' );

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