<?php

namespace Gettext\Tests;

use Gettext\Translations;
use PHPUnit_Framework_TestCase;

abstract class AbstractTest extends PHPUnit_Framework_TestCase
{
    protected static $directory = '1';
    protected static $files = [
        'Blade' => 'php',
        'Csv' => 'csv',
        'CsvDictionary' => 'csv',
        'Jed' => 'json',
        'JsCode' => 'js',
        'JsonDictionary' => 'json',
        'Mo' => 'mo',
        'PhpArray' => 'php',
        'PhpCode' => 'php',
        'Po' => 'po',
        'Twig' => 'php',
        'Yaml' => 'yml',
        'YamlDictionary' => 'yml',
    ];

    protected static function asset($file)
    {
        return './tests/assets/'.$file;
    }

    protected static function file($format, $prefix = '')
    {
        return static::asset(static::$directory.'/'.$prefix.$format.'.'.static::$files[$format]);
    }

    protected static function getInput($format)
    {
        $method = "from{$format}File";

        return Translations::$method(static::file($format, 'Input.'));
    }

    protected static function get($format, $prefix = '')
    {
        $method = "from{$format}File";

        return Translations::$method(static::file($format, $prefix));
    }

    protected static function content($format)
    {
        return file_get_contents(static::file($format));
    }

    protected static function save(Translations $translations, $format)
    {
        $method = "to{$format}String";

        file_put_contents(static::file($format), $translations->$method());
    }

    protected function assertContent(Translations $translations, $format)
    {
        $method = "to{$format}String";

        $this->assertSame(static::content($format), $translations->$method());
    }
}
