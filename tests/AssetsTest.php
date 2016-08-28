<?php

namespace Gettext\Tests;

class AssetsTest extends AbstractTest
{
    public function testPo()
    {
        $translations = static::get('po/input', 'Po');
        $countTranslations = 3;
        $countHeaders = 9;

        $this->assertCount($countTranslations, $translations);
        $this->assertCount($countHeaders, $translations->getHeaders());

        $this->assertContent($translations, 'po/Po');
        $this->assertContent($translations, 'po/Mo');
        $this->assertContent($translations, 'po/PhpArray');
        $this->assertContent($translations, 'po/Jed');
        $this->assertContent($translations, 'po/Json');
        $this->assertContent($translations, 'po/JsonDictionary');
        $this->assertContent($translations, 'po/Csv');
        $this->assertContent($translations, 'po/CsvDictionary');
        $this->assertContent($translations, 'po/Xliff');
        $this->assertContent($translations, 'po/Yaml');
        $this->assertContent($translations, 'po/YamlDictionary');

        $this->runTestFormat('po/Po', $countTranslations, $countHeaders);
        $this->runTestFormat('po/Mo', $countTranslations, $countHeaders);
        $this->runTestFormat('po/PhpArray', $countTranslations, $countHeaders);
        $this->runTestFormat('po/Jed', $countTranslations, 10);
        $this->runTestFormat('po/Xliff', $countTranslations, $countHeaders);
        $this->runTestFormat('po/Json', $countTranslations, $countHeaders);
        $this->runTestFormat('po/JsonDictionary', $countTranslations);
        $this->runTestFormat('po/Csv', $countTranslations, $countHeaders);
        $this->runTestFormat('po/CsvDictionary', $countTranslations);
        $this->runTestFormat('po/Yaml', $countTranslations, $countHeaders);
        $this->runTestFormat('po/YamlDictionary', $countTranslations);
    }

    public function testPo2()
    {
        $translations = static::get('po2/input', 'Po');
        $countTranslations = 13;
        $countHeaders = 13;

        $this->assertCount($countTranslations, $translations);
        $this->assertCount($countHeaders, $translations->getHeaders());

        $this->assertContent($translations, 'po2/Po');
        $this->assertContent($translations, 'po2/Mo');
        $this->assertContent($translations, 'po2/PhpArray');
        $this->assertContent($translations, 'po2/Jed');
        $this->assertContent($translations, 'po2/Json');
        $this->assertContent($translations, 'po2/JsonDictionary');
        $this->assertContent($translations, 'po2/Csv');
        $this->assertContent($translations, 'po2/CsvDictionary');
        $this->assertContent($translations, 'po2/Xliff');
        $this->assertContent($translations, 'po2/Yaml');
        $this->assertContent($translations, 'po2/YamlDictionary');

        $this->runTestFormat('po2/Po', $countTranslations, $countHeaders);
        $this->runTestFormat('po2/Mo', $countTranslations - 3, $countHeaders);
        $this->runTestFormat('po2/PhpArray', $countTranslations, $countHeaders);
        $this->runTestFormat('po2/Jed', $countTranslations, 10);
        $this->runTestFormat('po2/Xliff', $countTranslations, $countHeaders);
        $this->runTestFormat('po2/Json', $countTranslations, $countHeaders);
        $this->runTestFormat('po2/JsonDictionary', $countTranslations);
        $this->runTestFormat('po2/Csv', $countTranslations, $countHeaders);
        $this->runTestFormat('po2/CsvDictionary', $countTranslations);
        $this->runTestFormat('po2/Yaml', $countTranslations, $countHeaders);
        $this->runTestFormat('po2/YamlDictionary', $countTranslations);
    }

    public function testPo3()
    {
        $translations = static::get('po3/input', 'Po');
        $countTranslations = 8;
        $countHeaders = 8;

        $this->assertCount($countTranslations, $translations);
        $this->assertCount($countHeaders, $translations->getHeaders());

        $this->assertContent($translations, 'po3/Po');
        $this->assertContent($translations, 'po3/Mo');
        $this->assertContent($translations, 'po3/PhpArray');
        $this->assertContent($translations, 'po3/Jed');
        $this->assertContent($translations, 'po3/Json');
        $this->assertContent($translations, 'po3/JsonDictionary');
        $this->assertContent($translations, 'po3/Csv');
        $this->assertContent($translations, 'po3/CsvDictionary');
        $this->assertContent($translations, 'po3/Xliff');
        $this->assertContent($translations, 'po3/Yaml');
        $this->assertContent($translations, 'po3/YamlDictionary');

        $this->runTestFormat('po3/Po', $countTranslations, $countHeaders);
        $this->runTestFormat('po3/Mo', $countTranslations, $countHeaders);
        $this->runTestFormat('po3/PhpArray', $countTranslations, $countHeaders);
        $this->runTestFormat('po3/Jed', $countTranslations, 10);
        $this->runTestFormat('po3/Xliff', $countTranslations, $countHeaders);
        $this->runTestFormat('po3/Json', $countTranslations, $countHeaders);
        $this->runTestFormat('po3/JsonDictionary', $countTranslations);
        $this->runTestFormat('po3/Csv', $countTranslations, $countHeaders);
        $this->runTestFormat('po3/CsvDictionary', $countTranslations);
        $this->runTestFormat('po3/Yaml', $countTranslations, $countHeaders);
        $this->runTestFormat('po3/YamlDictionary', $countTranslations);
    }

    public function testBlade()
    {
        $translations = static::get('blade/input', 'Blade');
        $countTranslations = 11;
        $countHeaders = 8;

        $this->assertCount($countTranslations, $translations);
        $this->assertCount($countHeaders, $translations->getHeaders());

        $this->assertContent($translations, 'blade/Po');
        $this->assertContent($translations, 'blade/Mo');
        $this->assertContent($translations, 'blade/PhpArray');
        $this->assertContent($translations, 'blade/Jed');
        $this->assertContent($translations, 'blade/Json');
        $this->assertContent($translations, 'blade/JsonDictionary');
        $this->assertContent($translations, 'blade/Csv');
        $this->assertContent($translations, 'blade/CsvDictionary');
        $this->assertContent($translations, 'blade/Xliff');
        $this->assertContent($translations, 'blade/Yaml');
        $this->assertContent($translations, 'blade/YamlDictionary');

        $this->runTestFormat('blade/Po', $countTranslations, $countHeaders);
        $this->runTestFormat('blade/Mo', 0, $countHeaders);
        $this->runTestFormat('blade/PhpArray', $countTranslations, $countHeaders);
        $this->runTestFormat('blade/Jed', $countTranslations, 10);
        $this->runTestFormat('blade/Xliff', $countTranslations, $countHeaders);
        $this->runTestFormat('blade/Json', $countTranslations, $countHeaders);
        $this->runTestFormat('blade/JsonDictionary', $countTranslations);
        $this->runTestFormat('blade/Csv', $countTranslations, $countHeaders);
        $this->runTestFormat('blade/CsvDictionary', $countTranslations);
        $this->runTestFormat('blade/Yaml', $countTranslations, $countHeaders);
        $this->runTestFormat('blade/YamlDictionary', $countTranslations);
    }

    public function testJed()
    {
        $translations = static::get('jed/input', 'Jed');
        $countTranslations = 13;
        $countHeaders = 10;

        $this->assertCount($countTranslations, $translations);
        $this->assertCount($countHeaders, $translations->getHeaders());

        $this->assertContent($translations, 'jed/Po');
        $this->assertContent($translations, 'jed/Mo');
        $this->assertContent($translations, 'jed/PhpArray');
        $this->assertContent($translations, 'jed/Jed');
        $this->assertContent($translations, 'jed/Json');
        $this->assertContent($translations, 'jed/JsonDictionary');
        $this->assertContent($translations, 'jed/Csv');
        $this->assertContent($translations, 'jed/CsvDictionary');
        $this->assertContent($translations, 'jed/Xliff');
        $this->assertContent($translations, 'jed/Yaml');
        $this->assertContent($translations, 'jed/YamlDictionary');

        $this->runTestFormat('jed/Po', $countTranslations, $countHeaders);
        $this->runTestFormat('jed/Mo', 10, $countHeaders);
        $this->runTestFormat('jed/PhpArray', $countTranslations, $countHeaders);
        $this->runTestFormat('jed/Jed', $countTranslations, 10);
        $this->runTestFormat('jed/Xliff', $countTranslations, $countHeaders);
        $this->runTestFormat('jed/Json', $countTranslations, $countHeaders);
        $this->runTestFormat('jed/JsonDictionary', $countTranslations);
        $this->runTestFormat('jed/Csv', $countTranslations, $countHeaders);
        $this->runTestFormat('jed/CsvDictionary', $countTranslations);
        $this->runTestFormat('jed/Yaml', $countTranslations, $countHeaders);
        $this->runTestFormat('jed/YamlDictionary', $countTranslations);
    }

    public function testJsCode()
    {
        $translations = static::get('jscode/input', 'JsCode');
        $countTranslations = 7;
        $countHeaders = 8;

        $this->assertCount($countTranslations, $translations);
        $this->assertCount($countHeaders, $translations->getHeaders());

        $this->assertContent($translations, 'jscode/Po');
        $this->assertContent($translations, 'jscode/Mo');
        $this->assertContent($translations, 'jscode/PhpArray');
        $this->assertContent($translations, 'jscode/Jed');
        $this->assertContent($translations, 'jscode/Json');
        $this->assertContent($translations, 'jscode/JsonDictionary');
        $this->assertContent($translations, 'jscode/Csv');
        $this->assertContent($translations, 'jscode/CsvDictionary');
        $this->assertContent($translations, 'jscode/Xliff');
        $this->assertContent($translations, 'jscode/Yaml');
        $this->assertContent($translations, 'jscode/YamlDictionary');

        $this->runTestFormat('jscode/Po', $countTranslations, $countHeaders);
        $this->runTestFormat('jscode/Mo', 0, $countHeaders);
        $this->runTestFormat('jscode/PhpArray', $countTranslations, $countHeaders);
        $this->runTestFormat('jscode/Jed', $countTranslations, 10);
        $this->runTestFormat('jscode/Xliff', $countTranslations, $countHeaders);
        $this->runTestFormat('jscode/Json', $countTranslations, $countHeaders);
        $this->runTestFormat('jscode/JsonDictionary', $countTranslations);
        $this->runTestFormat('jscode/Csv', $countTranslations, $countHeaders);
        $this->runTestFormat('jscode/CsvDictionary', $countTranslations);
        $this->runTestFormat('jscode/Yaml', $countTranslations, $countHeaders);
        $this->runTestFormat('jscode/YamlDictionary', $countTranslations);
    }

    public function testJs2Code()
    {
        $translations = static::get('jscode2/input', 'JsCode');
        $countTranslations = 3;
        $countHeaders = 8;

        $this->assertCount($countTranslations, $translations);
        $this->assertCount($countHeaders, $translations->getHeaders());

        $this->assertContent($translations, 'jscode2/Po');
        $this->assertContent($translations, 'jscode2/Mo');
        $this->assertContent($translations, 'jscode2/PhpArray');
        $this->assertContent($translations, 'jscode2/Jed');
        $this->assertContent($translations, 'jscode2/Json');
        $this->assertContent($translations, 'jscode2/JsonDictionary');
        $this->assertContent($translations, 'jscode2/Csv');
        $this->assertContent($translations, 'jscode2/CsvDictionary');
        $this->assertContent($translations, 'jscode2/Xliff');
        $this->assertContent($translations, 'jscode2/Yaml');
        $this->assertContent($translations, 'jscode2/YamlDictionary');

        $this->runTestFormat('jscode2/Po', $countTranslations, $countHeaders);
        $this->runTestFormat('jscode2/Mo', 0, $countHeaders);
        $this->runTestFormat('jscode2/PhpArray', $countTranslations, $countHeaders);
        $this->runTestFormat('jscode2/Jed', $countTranslations, 10);
        $this->runTestFormat('jscode2/Xliff', $countTranslations, $countHeaders);
        $this->runTestFormat('jscode2/Json', $countTranslations, $countHeaders);
        $this->runTestFormat('jscode2/JsonDictionary', $countTranslations);
        $this->runTestFormat('jscode2/Csv', $countTranslations, $countHeaders);
        $this->runTestFormat('jscode2/CsvDictionary', $countTranslations);
        $this->runTestFormat('jscode2/Yaml', $countTranslations, $countHeaders);
        $this->runTestFormat('jscode2/YamlDictionary', $countTranslations);
    }

    public function testPhpCode()
    {
        $translations = static::get('phpcode/input', 'PhpCode');
        $countTranslations = 12;
        $countHeaders = 8;

        $this->assertCount($countTranslations, $translations);
        $this->assertCount($countHeaders, $translations->getHeaders());

        $this->assertContent($translations, 'phpcode/Po');
        $this->assertContent($translations, 'phpcode/Mo');
        $this->assertContent($translations, 'phpcode/PhpArray');
        $this->assertContent($translations, 'phpcode/Jed');
        $this->assertContent($translations, 'phpcode/Json');
        $this->assertContent($translations, 'phpcode/JsonDictionary');
        $this->assertContent($translations, 'phpcode/Csv');
        $this->assertContent($translations, 'phpcode/CsvDictionary');
        $this->assertContent($translations, 'phpcode/Xliff');
        $this->assertContent($translations, 'phpcode/Yaml');
        $this->assertContent($translations, 'phpcode/YamlDictionary');

        $this->runTestFormat('phpcode/Po', $countTranslations, $countHeaders);
        $this->runTestFormat('phpcode/Mo', 0, $countHeaders);
        $this->runTestFormat('phpcode/PhpArray', $countTranslations, $countHeaders);
        $this->runTestFormat('phpcode/Jed', $countTranslations, 10);
        $this->runTestFormat('phpcode/Xliff', $countTranslations, $countHeaders);
        $this->runTestFormat('phpcode/Json', $countTranslations, $countHeaders);
        $this->runTestFormat('phpcode/JsonDictionary', $countTranslations);
        $this->runTestFormat('phpcode/Csv', $countTranslations, $countHeaders);
        $this->runTestFormat('phpcode/CsvDictionary', $countTranslations);
        $this->runTestFormat('phpcode/Yaml', $countTranslations, $countHeaders);
        $this->runTestFormat('phpcode/YamlDictionary', $countTranslations);
    }

    public function testPhpCode2()
    {
        $translations = static::get('phpcode2/input', 'PhpCode');
        $countTranslations = 9;
        $countHeaders = 8;

        $this->assertCount($countTranslations, $translations);
        $this->assertCount($countHeaders, $translations->getHeaders());

        $this->assertContent($translations, 'phpcode2/Po');
        $this->assertContent($translations, 'phpcode2/Mo');
        $this->assertContent($translations, 'phpcode2/PhpArray');
        $this->assertContent($translations, 'phpcode2/Jed');
        $this->assertContent($translations, 'phpcode2/Json');
        $this->assertContent($translations, 'phpcode2/JsonDictionary');
        $this->assertContent($translations, 'phpcode2/Csv');
        $this->assertContent($translations, 'phpcode2/CsvDictionary');
        $this->assertContent($translations, 'phpcode2/Xliff');
        $this->assertContent($translations, 'phpcode2/Yaml');
        $this->assertContent($translations, 'phpcode2/YamlDictionary');

        $this->runTestFormat('phpcode2/Po', $countTranslations, $countHeaders);
        $this->runTestFormat('phpcode2/Mo', 0, $countHeaders);
        $this->runTestFormat('phpcode2/PhpArray', $countTranslations, $countHeaders);
        $this->runTestFormat('phpcode2/Jed', $countTranslations, 10);
        $this->runTestFormat('phpcode2/Xliff', $countTranslations, $countHeaders);
        $this->runTestFormat('phpcode2/Json', $countTranslations, $countHeaders);
        $this->runTestFormat('phpcode2/JsonDictionary', $countTranslations);
        $this->runTestFormat('phpcode2/Csv', $countTranslations, $countHeaders);
        $this->runTestFormat('phpcode2/CsvDictionary', $countTranslations);
        $this->runTestFormat('phpcode2/Yaml', $countTranslations, $countHeaders);
        $this->runTestFormat('phpcode2/YamlDictionary', $countTranslations);
    }

    public function testTwig()
    {
        $translations = static::get('twig/input', 'Twig');
        $countTranslations = 10;
        $countHeaders = 8;

        $this->assertCount($countTranslations, $translations);
        $this->assertCount($countHeaders, $translations->getHeaders());

        $this->assertContent($translations, 'twig/Po');
        $this->assertContent($translations, 'twig/Mo');
        $this->assertContent($translations, 'twig/PhpArray');
        $this->assertContent($translations, 'twig/Jed');
        $this->assertContent($translations, 'twig/Json');
        $this->assertContent($translations, 'twig/JsonDictionary');
        $this->assertContent($translations, 'twig/Csv');
        $this->assertContent($translations, 'twig/CsvDictionary');
        $this->assertContent($translations, 'twig/Xliff');
        $this->assertContent($translations, 'twig/Yaml');
        $this->assertContent($translations, 'twig/YamlDictionary');

        $this->runTestFormat('twig/Po', $countTranslations, $countHeaders);
        $this->runTestFormat('twig/Mo', 0, $countHeaders);
        $this->runTestFormat('twig/PhpArray', $countTranslations, $countHeaders);
        $this->runTestFormat('twig/Jed', $countTranslations, 10);
        $this->runTestFormat('twig/Xliff', $countTranslations, $countHeaders);
        $this->runTestFormat('twig/Json', $countTranslations, $countHeaders);
        $this->runTestFormat('twig/JsonDictionary', $countTranslations);
        $this->runTestFormat('twig/Csv', $countTranslations, $countHeaders);
        $this->runTestFormat('twig/CsvDictionary', $countTranslations);
        $this->runTestFormat('twig/Yaml', $countTranslations, $countHeaders);
        $this->runTestFormat('twig/YamlDictionary', $countTranslations);
    }
}
