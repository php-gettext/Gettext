<?php

namespace Gettext\Tests;

use Gettext\Translations;
use PHPUnit_Framework_TestCase;

abstract class AbstractTest extends PHPUnit_Framework_TestCase
{
    protected static function asset($file)
    {
        return './tests/assets/'.$file;
    }

    protected static function file($format)
    {
        return static::asset(static::$file.'.'.$format);
    }

    protected static function content($format)
    {
        return file_get_contents(static::file($format));
    }

    protected static function save($content, $format)
    {
        file_put_contents(static::file($format), $content);
    }

    protected static function saveAsserts(Translations $translations)
    {
        static::save($translations->toPoString(), 'po');
        static::save($translations->toMoString(), 'mo');
        static::save($translations->toPhpArrayString(), 'php');
        static::save($translations->toJedString(), 'jed');
        static::save($translations->toJsonDictionaryString(), 'json');
        static::save($translations->toCsvDictionaryString(), 'csv');
    }
}
