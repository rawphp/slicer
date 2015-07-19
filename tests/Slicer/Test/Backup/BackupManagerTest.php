<?php

namespace Slicer\Test\Backup;

use Slicer\Backup\BackupManager;
use Slicer\Contract\IBackupManager;
use Slicer\Event\PostBackupEvent;
use Slicer\Event\PreBackupEvent;
use Slicer\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use ZipArchive;

/**
 * Class BackupManagerTest
 *
 * @package Slicer\Test\Backup
 */
class BackupManagerTest extends TestCase
{
    /** @var  IBackupManager */
    protected $manager;
    /** @var  bool */
    protected $preVerified = FALSE;
    /** @var  bool */
    protected $postVerified = FALSE;

    /** @var  string */
    protected static $backupFile;
    /** @var  array */
    protected static $backupOptions = [ ];

    /**
     * This method is called before the first test of this test class is run.
     *
     * @since Method available since Release 3.4.0
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$backupFile = 'test-backup.zip';

        self::$backupOptions =
            [
                'backup-file' => self::$backupFile,
                'backup-type' => 'full',
                'output'      =>
                    [
                        'debug' => FALSE,
                    ]
            ];
    }

    /**
     * Setup before each test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->manager = new BackupManager( $this->getConfig() );
        $this->manager->setEventDispatcher( new EventDispatcher() );

        $this->preVerified  = FALSE;
        $this->postVerified = FALSE;
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();

        if ( file_exists( self::$backupFile ) )
        {
            unlink( self::$backupFile );
        }

        if ( file_exists( base_path( 'extracted' ) ) )
        {
            ( new Filesystem() )->remove( [ base_path( 'extracted' ) ] );
        }
    }

    /**
     * Test backup manager instantiated.
     */
    public function testBackupManagerInstantiatedCorrectly()
    {
        $this->assertNotNull( $this->manager );
    }

    /**
     * Test backup with pre- and post- backup events.
     */
    public function testBackupWithPreAndPostBackupEvent()
    {
        $this->manager->getEventDispatcher()->addListener( PreBackupEvent::class, function ( PreBackupEvent $event )
        {
            $this->assertInstanceOf( PreBackupEvent::class, $event );
            $this->assertNotNull( $event->getOptions() );
            $this->preVerified = TRUE;
        }
        );

        $this->manager->getEventDispatcher()->addListener( PostBackupEvent::class, function ( PostBackupEvent $event )
        {
            $this->assertInstanceOf( PostBackupEvent::class, $event );
            $this->assertNotNull( $event->getArchiveLocation() );
            $this->assertNotNull( $event->getOptions() );
            $this->postVerified = TRUE;
        }
        );

        $this->manager->backup( self::$backupOptions );

        $this->assertTrue( $this->preVerified );
        $this->assertTrue( $this->postVerified );
    }

    /**
     * Test backup and extract and check files.
     */
    public function testBackupAndCheckFiles()
    {
        $this->manager->getEventDispatcher()->addListener( PostBackupEvent::class, function ( PostBackupEvent $event )
        {
            $this->assertNotEmpty( $event->getArchiveLocation() );

            $zip = new ZipArchive();
            $zip->open( $event->getArchiveLocation() );

            $this->assertEquals( 'No error', $zip->getStatusString() );

            mkdir( base_path( 'extracted' ) );

            $this->assertTrue( $zip->extractTo( base_path( 'extracted' ) ) );

            $this->dirHasFile( base_path( 'extracted' ), 'bin' );
            $this->dirHasFile( base_path( 'extracted' ), 'res' );
            $this->dirHasFile( base_path( 'extracted' ), 'src' );
            $this->dirHasFile( base_path( 'extracted' ), 'src/Slicer' );
            $this->dirHasFile( base_path( 'extracted' ), 'src/Slicer/Backup' );
            $this->dirHasFile( base_path( 'extracted' ), 'src/Slicer/Command' );
            $this->dirHasFile( base_path( 'extracted' ), 'src/Slicer/Command/BackupCommand.php' );
            $this->dirHasFile( base_path( 'extracted' ), 'tests' );
            $this->dirHasFile( base_path( 'extracted' ), 'vendor' );
            $this->dirHasFile( base_path( 'extracted' ), 'composer.json' );
            $this->dirHasFile( base_path( 'extracted' ), 'composer.lock' );
            $this->dirHasFile( base_path( 'extracted' ), 'LICENSE' );
            $this->dirHasFile( base_path( 'extracted' ), 'phpunit.xml.dist' );
            $this->dirHasFile( base_path( 'extracted' ), 'slicer.json' );

            $this->postVerified = TRUE;
        }
        );

        $this->manager->backup( self::$backupOptions );

        $this->assertTrue( $this->postVerified );
    }

    /**
     * Helper method to check for file in a directory.
     *
     * @param string $dir
     * @param string $file
     */
    private function dirHasFile( $dir, $file )
    {
        $this->assertTrue( file_exists( clean_slicer_path( $dir . '/' . $file ) ) );
    }
}