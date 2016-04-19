<?php

abstract class AbstractTest extends PHPUnit_Framework_TestCase
{
    public static function file($format)
    {
        return __DIR__.'/assets/'.static::$file.'.'.$format;
    }

    public static function content($format)
    {
        return file_get_contents(static::file($format));
    }
}
