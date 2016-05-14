<?php

namespace Gettext\Tests;

use Gettext\Translations;

class Asset1Test extends AbstractTest
{
    protected static $directory = '1';
    protected static $input = 'Po';

    const COUNT_TRANSLATIONS = 3;
    const COUNT_EMPTY_TRANSLATIONS = 0;
    const COUNT_HEADERS = 9;

    public function testParser()
    {
        $translations = static::getInput(static::$input);

        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertCount(static::COUNT_HEADERS, $translations->getHeaders());

        $this->assertContent($translations, 'Po');
        $this->assertContent($translations, 'Mo');
        $this->assertContent($translations, 'PhpArray');
        $this->assertContent($translations, 'Jed');
        $this->assertContent($translations, 'JsonDictionary');
        $this->assertContent($translations, 'CsvDictionary');
        $this->assertContent($translations, 'Xliff');
        $this->assertContent($translations, 'YamlDictionary');

        return $translations;
    }

    public function testPo()
    {
        $translations = static::get('Po');

        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertCount(static::COUNT_HEADERS, $translations->getHeaders());
        $this->assertContent($translations, 'Po');
    }

    public function testMo()
    {
        $translations = static::get('Mo');

        $this->assertCount(static::COUNT_TRANSLATIONS - static::COUNT_EMPTY_TRANSLATIONS, $translations);
        $this->assertCount(static::COUNT_HEADERS, $translations->getHeaders());
        $this->assertContent($translations, 'Mo');
    }

    public function testPhpArray()
    {
        $translations = static::get('PhpArray');

        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertContent($translations, 'PhpArray');
    }

    public function testJed()
    {
        $translations = static::get('Jed');

        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertContent($translations, 'Jed');
    }

    public function testJsonDictionary()
    {
        $translations = static::get('JsonDictionary');

        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertContent($translations, 'JsonDictionary');
    }

    public function testCsvDictionary()
    {
        $translations = static::get('CsvDictionary');

        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertContent($translations, 'CsvDictionary');
    }

    public function testCsv()
    {
        $translations = static::get('Csv');

        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertContent($translations, 'Csv');
    }

    public function testYamlDictionary()
    {
        $translations = static::get('YamlDictionary');

        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertContent($translations, 'YamlDictionary');
    }

    public function testXliff()
    {
        $translations = static::get('Xliff');

        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertContent($translations, 'Xliff');
    }

    public function testYaml()
    {
        $translations = static::get('Yaml');

        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertContent($translations, 'Yaml');
    }
}
