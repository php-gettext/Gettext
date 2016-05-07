<?php

namespace Gettext\Tests;

use Gettext\Translations;

class OneTest extends AbstractTest
{
    protected static $file = 'one';

    const COUNT_TRANSLATIONS = 3;
    const COUNT_EMPTY_TRANSLATIONS = 0;
    const COUNT_HEADERS = 9;

    protected function getParsed()
    {
        return Translations::fromPoFile(static::file('raw.po'));
    }

    public function testParser()
    {
        $translations = static::getParsed();
        //static::saveAsserts($translations);

        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertCount(static::COUNT_HEADERS, $translations->getHeaders());

        $this->assertSame(static::content('po'), $translations->toPoString());
        $this->assertSame(static::content('mo'), $translations->toMoString());
        $this->assertSame(static::content('php'), $translations->toPhpArrayString());
        $this->assertSame(static::content('jed'), $translations->toJedString());
        $this->assertSame(static::content('json'), $translations->toJsonDictionaryString());
        $this->assertSame(static::content('csv'), $translations->toCsvDictionaryString());
        $this->assertSame(static::content('yml'), $translations->toYamlDictionaryString());

        return $translations;
    }

    public function testPo()
    {
        $translations = Translations::fromPoFile(static::file('po'));

        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertCount(static::COUNT_HEADERS, $translations->getHeaders());

        $this->assertSame(static::content('po'), $translations->toPoString());
    }

    public function testMo()
    {
        $translations = Translations::fromMoFile(static::file('mo'));

        $this->assertCount(static::COUNT_TRANSLATIONS - static::COUNT_EMPTY_TRANSLATIONS, $translations);
        $this->assertCount(static::COUNT_HEADERS, $translations->getHeaders());

        $this->assertSame(static::content('mo'), $translations->toMoString());
    }

    public function testPhpArray()
    {
        $translations = Translations::fromPhpArrayFile(static::file('php'));

        //translations + headers
        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertSame(static::content('php'), $translations->toPhpArrayString());
    }

    public function testJed()
    {
        $translations = Translations::fromJedFile(static::file('jed'));

        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertSame(static::content('jed'), $translations->toJedString());
    }

    public function testJsonDictionary()
    {
        $translations = Translations::fromJsonDictionaryFile(static::file('json'));

        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertSame(static::content('json'), $translations->toJsonDictionaryString());
    }

    public function testCsvDictionary()
    {
        $translations = Translations::fromCsvDictionaryFile(static::file('csv'));

        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertSame(static::content('csv'), $translations->toCsvDictionaryString());
    }

    public function testYamlDictionary()
    {
        $translations = Translations::fromYamlDictionaryFile(static::file('yml'));

        $this->assertCount(static::COUNT_TRANSLATIONS, $translations);
        $this->assertSame(static::content('yml'), $translations->toYamlDictionaryString());
    }
}
