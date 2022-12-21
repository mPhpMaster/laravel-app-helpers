<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use Illuminate\Filesystem\Filesystem;

if( !function_exists('filenameWithoutExtension') ) {
    /**
     * returns the given filename without extension
     *
     * @param string $filename
     *
     * @return string|string[]|null
     */
    function filenameWithoutExtension(string $filename): array|string|null
    {
        return $filename ? pathinfo($filename, PATHINFO_FILENAME) : null;
    }
}

if( !function_exists('includeSubFiles') ) {
    /**
     * Include php files
     */
    function includeSubFiles($__DIR__, $__FILE__ = null, callable $incCallBack = null): void
    {
        $__FILE__ = $__FILE__ ? rtrim(basename($__FILE__), '.php') : "";
        $__DIR__ = $__DIR__ ? rtrim($__DIR__, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : "";
        $sub_path = $__DIR__ . /*($__DIR__ && $__FILE__ ? DIRECTORY_SEPARATOR : "") .*/
            $__FILE__;

        if( file_exists($sub_path) ) {
            collect((new Filesystem)->files($sub_path))
                ->map(function($v) use ($incCallBack) {
                    if( strtolower(trim($v->getExtension())) !== 'php' ) {
                        return false;
                    }

                    if( $incCallBack && is_callable($incCallBack) ) {
                        $incCallBack($v->getPathname());
                    }

                    include_once $v->getPathname();
                });
        }
    }
}

if( !function_exists('includeAllSubFiles') ) {
    /**
     * Include php files
     */
    function includeAllSubFiles($__DIR__, $__FILE__ = "", callable $incCallBack = null)
    {
        $__DIR__ = rtrim($__DIR__, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $__FILE__;

        if( file_exists($__DIR__) ) {
            return collect((new Filesystem)->allFiles($__DIR__))
                ->map(function($v) use ($incCallBack) {
                    /** @var \Symfony\Component\Finder\SplFileInfo $v */
                    if( $v->getExtension() !== 'php' ) {
                        return false;
                    }

                    if( $incCallBack && is_callable($incCallBack) ) {
                        return $incCallBack($v->getPathname());
                    }

                    return includeIfExists($v->getPathname());
                });
        }

        return collect();
    }
}

if( !function_exists('includeIfExists') ) {
    /**
     * Include file if exist
     *
     * @param string              $file
     * @param callable|null|mixed $when_not_exists
     *
     * @return null|mixed
     */
    function includeIfExists(string $file, mixed $when_not_exists = null)
    {
        return file_exists($file) ? include($file) : getValue($when_not_exists);
    }
}

if( !function_exists('includeOnceIfExists') ) {
    /**
     * Include file Once if exist
     *
     * @param string              $file
     * @param callable|mixed|null $when_not_exists
     * @param callable|mixed|null $when_already_included
     *
     * @return bool|mixed
     */
    function includeOnceIfExists(string $file, mixed $when_not_exists = null, mixed $when_already_included = null)
    {
        if( file_exists($file) ) {
            if( ($return = include_once($file)) === true ) {
                $return = $when_already_included instanceof Closure ? getValue($when_already_included, ...[ $file ]) : $when_already_included;
            }
        } else {
            $return = $when_not_exists = $when_not_exists instanceof Closure ? getValue($when_not_exists, ...[ $file ]) : $when_not_exists;
        }

        return getValue($return, ...[ $file ]);
    }
}

if( !function_exists('fixPath') ) {
    /**
     * Fix slashes/back-slashes replace it with DIRECTORY_SEPARATOR.
     *
     * @param string $path
     * @param string $replace_separator_with
     *
     * @return string
     */
    function fixPath(string $path, $replace_separator_with = DIRECTORY_SEPARATOR): string
    {
        $replace_separator_with = $replace_separator_with ?: DIRECTORY_SEPARATOR;

        return replaceAll([
                              "\\" => $replace_separator_with,
                              "/" => $replace_separator_with,
                              $replace_separator_with . $replace_separator_with => $replace_separator_with,
                          ], $path);
    }
}
