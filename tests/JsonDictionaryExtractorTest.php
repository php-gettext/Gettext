<?php

include_once dirname(__DIR__).'/src/autoloader.php';

class JsonDictionaryExtractorTest extends PHPUnit_Framework_TestCase
{
    public function testGeneration()
    {
        $string = '{"apple": "maza", "water": "auga"}';

        //Extract translations
        $translations = Gettext\Translations::fromJsonDictionaryString($string);

        $this->assertInstanceOf('Gettext\\Translations', $translations);
        $this->assertEquals(2, count($translations));

        $translation = $translations->find('', 'water');

        $this->assertInstanceOf('Gettext\\Translation', $translation);
        $this->assertEquals('auga', $translation->getTranslation());
    }
}
