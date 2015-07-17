<?php

if ( !function_exists( 'includeIfExists' ) )
{
    function includeIfExists( $file )
    {
        return file_exists( $file ) ? include $file : FALSE;
    }
}

if ( ( !$loader = includeIfExists( __DIR__ . '/../vendor/autoload.php' ) ) && ( !$loader = includeIfExists( __DIR__ . '/../../../autoload.php' ) ) )
{
    echo 'You must set up the project dependencies, run the following commands:' . PHP_EOL .
        'curl -sS https://getslicer.com/installer | php' . PHP_EOL .
        'php slicer.phar install' . PHP_EOL;

    exit( 1 );
}

return $loader;