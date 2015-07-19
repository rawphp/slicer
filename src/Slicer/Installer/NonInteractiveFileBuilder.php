<?php

namespace Slicer\Installer;

use Slicer\Contract\ISlicerFileBuilder;

/**
 * Class NonInteractiveFileBuilder
 *
 * @package Slicer\Installer
 */
class NonInteractiveFileBuilder implements ISlicerFileBuilder
{
    /**
     * Construct a default slicer file.
     *
     * @return string
     */
    public function buildFile()
    {
        return print_r( json_encode( $this->constructFileStructure(), JSON_PRETTY_PRINT ), TRUE );
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
        return
            [
                'app'             =>
                    [
                        'name'        => 'slicer/slicer',
                        'description' => 'Site update manager',
                        'app_key'     => '',
                        'app_secret'  => '',
                    ],
                'options'         =>
                    [
                        'update' =>
                            [
                                'backup-files'    => TRUE,
                                'backup-database' => TRUE,
                            ],
                    ],
                'update_file'     => 'Slicer\\Update',
                'change_provider' =>
                    [
                        'driver' => 'Git',
                        'class'  => 'Slicer\\Provider\\GitProvider',
                    ],
                'signing'         =>
                    [
                        'private_key' => 'private.key',
                        'public_key'  => 'public.pem',
                    ],
                'storage'         =>
                    [
                        'source'      =>
                            [
                                'tmp-dir' => 'slicer/tmp',
                            ],
                        'destination' =>
                            [
                                'update-dir' => 'slicer/updates',
                            ],
                    ],
                'backup'          =>
                    [
                        'exclude-dirs' => [ ],
                        'file-type'    => 'single',
                    ],
                'base_dir'        => '',
            ];
    }
}