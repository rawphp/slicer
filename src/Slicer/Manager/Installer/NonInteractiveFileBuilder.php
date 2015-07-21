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

use Slicer\Contract\ISlicerFileBuilder;

/**
 * Class NonInteractiveFileBuilder
 *
 * @package Slicer\Manager\Installer
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
                        'app-key'     => '',
                        'app-secret'  => '',
                    ],
                'options'         =>
                    [
                        'update' =>
                            [
                                'backup-files'    => TRUE,
                                'backup-database' => TRUE,
                            ],
                    ],
                'update-file'     =>
                    [
                        'class'     => 'Slicer\\Update',
                        'namespace' => 'Slicer\\Update',
                    ],
                'change-provider' =>
                    [
                        'driver' => 'Git',
                        'class'  => 'Slicer\\Provider\\GitProvider',
                    ],
                'signing'         =>
                    [
                        'private-key' => 'private.key',
                        'public-key'  => 'public.pem',
                    ],
                'storage'         =>
                    [
                        'tmp-dir'    => 'slicer/tmp',
                        'update-dir' => 'slicer/updates',
                        "backup-dir" => 'slicer/backup',
                    ],
                'backup'          =>
                    [
                        'exclude-dirs' => [ ],
                        'file-type'    => 'single',
                    ],
                'base-dir'        => '',
            ];
    }
}