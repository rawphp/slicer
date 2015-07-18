<?php

namespace Slicer;

use Composer\Package\Version\VersionParser;
use Composer\Package\AliasPackage;
use Composer\Package\LinkConstraint\VersionConstraint;
use Composer\Util\Filesystem;
use PHPUnit_Framework_TestCase;

/**
 * Class TestCase
 *
 * @package Slicer
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{
    private static $parser;

    protected static function getVersionParser()
    {
        if ( !self::$parser )
        {
            self::$parser = new VersionParser();
        }

        return self::$parser;
    }

    protected function getVersionConstraint( $operator, $version )
    {
        $constraint = new VersionConstraint(
            $operator,
            self::getVersionParser()->normalize( $version )
        );

        $constraint->setPrettyString( $operator . ' ' . $version );

        return $constraint;
    }

    protected function getPackage( $name, $version, $class = 'Composer\Package\Package' )
    {
        $normVersion = self::getVersionParser()->normalize( $version );

        return new $class( $name, $normVersion, $version );
    }

    protected function getAliasPackage( $package, $version )
    {
        $normVersion = self::getVersionParser()->normalize( $version );

        return new AliasPackage( $package, $normVersion, $version );
    }

    protected function ensureDirectoryExistsAndClear( $directory )
    {
        $fs = new Filesystem();
        if ( is_dir( $directory ) )
        {
            $fs->removeDirectory( $directory );
        }
        mkdir( $directory, 0777, TRUE );
    }
}
