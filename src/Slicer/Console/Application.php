<?php

namespace Slicer\Console;

use Exception;
use InvalidArgumentException;
use Slicer\Command\BackupCommand;
use Slicer\Command\CheckCommand;
use Slicer\Command\InitializeCommand;
use Slicer\Command\CreateCommand;
use Slicer\Command\PullUpdateCommand;
use Slicer\Command\PushUpdateCommand;
use Slicer\Command\UpdateCommand;
use Slicer\Factory;
use Slicer\Slicer;
use RuntimeException;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Application
 *
 * @package Slicer\Console
 */
class Application extends BaseApplication
{
    /** @var  Slicer */
    protected $slicer;
    /** @var  InputInterface */
    protected $input;
    /** @var  OutputInterface */
    protected $output;

    /**
     * Create a new application.
     */
    public function __construct()
    {
        if ( function_exists( 'ini_set' ) && extension_loaded( 'xdebug' ) )
        {
            ini_set( 'xdebug.show_exception_trace', FALSE );
            ini_set( 'xdebug.scream', FALSE );
        }

        if ( function_exists( 'date_default_timezone_set' ) && function_exists( 'date_default_timezone_get' ) )
        {
            date_default_timezone_set( @date_default_timezone_get() );
        }

        parent::__construct( 'Slicer', Slicer::VERSION );
    }

    /**
     * Runs the current application.
     *
     * @param InputInterface  $input  An Input instance
     * @param OutputInterface $output An Output instance
     *
     * @return int 0 if everything went fine, or an error code
     *
     * @throws Exception When doRun returns Exception
     *
     * @api
     */
    public function run( InputInterface $input = NULL, OutputInterface $output = NULL )
    {
        $this->input  = $input;
        $this->output = $output;

        if ( NULL === $output )
        {
        }

        return parent::run( $input, $output );
    }

    /**
     * Runs the current application.
     *
     * @param InputInterface  $input  An Input instance
     * @param OutputInterface $output An Output instance
     *
     * @return int 0 if everything went fine, or an error code
     */
    public function doRun( InputInterface $input, OutputInterface $output )
    {
        if ( 50302 > PHP_VERSION_ID )
        {
            $output->writeln( '<caution>Slicer only officially supports PHP 5.3.2 and above, you will most likely encounter problems with your PHP ' . PHP_VERSION . ', upgrading is strongly recommended.</caution>' );
        }

        if ( defined( 'SLICER_DEV_WARNING_TIME' ) )
        {
            $commandName = 'self-update';

        }

        if ( getenv( 'SLICER_NO_INTERACTION' ) )
        {
            $input->setInteractive( FALSE );
        }

        // switch to working dir
        if ( $newWorkingDir = $this->getNewWorkingDir( $input ) )
        {
            $oldWorkingDir = getcwd();

            chdir( $newWorkingDir );

//            if ( $this->getIO()->isDebug() >= 4 )
//            {
//                $this->getIO()->writeError( 'Changed CWD to ' . getcwd() );
//            }
        }

        if ( $input->hasParameterOption( '--profile' ) )
        {
            $startTime = microtime( TRUE );
            //$this->io->enableDebugging( $startTime );
        }

        $result = parent::doRun( $input, $output );

        if ( isset( $oldWorkingDir ) )
        {
            chdir( $oldWorkingDir );
        }

        if ( isset( $startTime ) )
        {
            $output->writeln( '<info>Memory usage: ' . round( memory_get_usage() / 1024 / 1024, 2 ) . 'MB (peak: ' . round( memory_get_peak_usage() / 1024 / 1024, 2 ) . 'MB), time: ' . round( microtime( TRUE ) - $startTime, 2 ) . 's</info>' );
        }

        return $result;
    }

    /**
     * Get working directory.
     *
     * @param InputInterface $input
     *
     * @return string
     */
    private function getNewWorkingDir( InputInterface $input )
    {
        $workingDir = $input->getParameterOption( [ '--working-dir', '-d' ] );

        if ( FALSE !== $workingDir && !is_dir( $workingDir ) )
        {
            throw new RuntimeException( 'Invalid working directory specified.' );
        }

        return $workingDir;
    }

    /**
     * @param bool|TRUE $required
     *
     * @return Slicer
     */
    public function getSlicer( $required = TRUE )
    {
        if ( NULL === $this->slicer )
        {
            try
            {
                $this->slicer = Factory::create();
            }
            catch ( InvalidArgumentException $e )
            {
                if ( $required && NULL !== $this->output )
                {
                    $this->output->writeln( $e->getMessage() );

                    exit( 1 );
                }
            }
        }

        return $this->slicer;
    }

    /**
     * Removes the cached slider instance.
     */
    public function resetSlicer()
    {
        $this->slicer = NULL;
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return Command[] An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        $commands = array_merge(
            parent::getDefaultCommands(),
            [
                new BackupCommand(),
                new CheckCommand(),
                new InitializeCommand(),
                new CreateCommand(),
                new UpdateCommand(),
                new PushUpdateCommand(),
                new PullUpdateCommand(),
            ]
        );

        if ( 'phar:' === substr( __FILE__, 0, 5 ) )
        {
            //$commands = new SelfUpdateCommand();
        }

        return $commands;
    }

    /**
     * Gets the default input definition.
     *
     * @return InputDefinition An InputDefinition instance
     */
    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();

        $definition->addOption( new InputOption( '--debug', NULL, InputOption::VALUE_NONE, 'Display debug information' ) );
        $definition->addOption( new InputOption( '--profile', NULL, InputOption::VALUE_NONE, 'Display timing and memory usage information' ) );
        $definition->addOption( new InputOption( '--working-dir', '-d', InputOption::VALUE_REQUIRED, 'If specified, use the given directory as working directory.' ) );

        return $definition;
    }
}