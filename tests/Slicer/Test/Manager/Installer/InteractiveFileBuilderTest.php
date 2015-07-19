<?php

namespace Slicer\Test\Manager\Installer;

use Slicer\Manager\Installer\InteractiveFileBuilder;
use Slicer\Provider\GitProvider;
use Slicer\TestCase;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Question\Question;

/**
 * Class InteractiveFileBuilderTest
 *
 * @package Slicer\Test\Manager\Installer
 */
class InteractiveFileBuilderTest extends TestCase
{
    /** @var  InteractiveFileBuilder */
    protected $builder;

    /**
     * Setup before each test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->builder = new InteractiveFileBuilder( new ArrayInput( [ ], NULL ), new ConsoleOutput(), new QuestionHelper() );
    }

    /**
     * Test initialization of the builder.
     */
    public function testInitialization()
    {
        $this->assertNotNull( $this->builder );
    }

    /**
     * Test get questions.
     */
    public function testGetQuestions()
    {
        $questions = $this->builder->getQuestions();

        $this->assertTrue( is_array( $questions ) );
        $this->assertNotEmpty( $questions );

        foreach ( $questions as $key => $question )
        {
            $this->assertTrue( is_string( $key ) );
            $this->assertInstanceOf( Question::class, $question );
        }
    }

    /**
     * Test get change provider.
     */
    public function testGetChangeProviderClass()
    {
        $provider = 'Git';

        $result = $this->builder->getChangeProviderClass( $provider );

        $this->assertEquals( GitProvider::class, $result );
    }

    /**
     * Test constructing file structure for slicer file.
     */
    public function testConstructFileStructure()
    {
        $data =
            [
                'app-name'        => '',
                'app-description' => '',
                'app-key'         => '',
                'app-secret'      => '',
                'backup-files'    => '',
                'backup-database' => '',
                'update-class'    => '',
                'change-provider' => 'Git',
                'private-key'     => '',
                'public-key'      => '',
                'backup-type'     => '',
                'backup-ignore'   => '',
            ];

        $file = $this->builder->constructFileStructure( $data );

        $this->assertTrue( is_array( $file ) );
    }
}