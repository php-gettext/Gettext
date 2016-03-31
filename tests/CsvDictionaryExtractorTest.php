<?php

include_once dirname(__DIR__).'/src/autoloader.php';

class CsvDictionaryExtractorTest extends PHPUnit_Framework_TestCase
{
    public function testGeneration()
    {
        $string = file_get_contents(__DIR__.'/files/generated.csv');

        //Extract translations
        $translations = Gettext\Translations::fromCsvDictionaryString($string);

        $this->assertInstanceOf('Gettext\\Translations', $translations);
        $this->assertEquals(12, count($translations));

        $translation = $translations->find('', 'text 2');

        $this->assertInstanceOf('Gettext\\Translation', $translation);
        $this->assertEquals('apple', $translation->getTranslation());
    }
}
