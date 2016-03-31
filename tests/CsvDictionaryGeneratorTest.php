<?php

include_once dirname(__DIR__).'/src/autoloader.php';

class CsvDictionaryGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testGeneration()
    {
        //Extract translations
        $translations = Gettext\Extractors\PhpCode::fromFile(__DIR__.'/files/phpcode.php');
        //verify existance of extracted translations
        $this->assertEquals(12, count($translations));
        $translation = $translations->find('', 'text 2');
        $this->assertInstanceOf('Gettext\\Translation', $translation);
        //set translation
        $translation->setTranslation('apple');
        //set plural
        $translation->setPlural('apples');
        //generate csv dict - skips meta and plurals
        $csv = Gettext\Generators\CsvDictionary::toString($translations);
        file_put_contents(__DIR__.'/files/generated.csv', $csv);
        $this->assertEquals(file_get_contents(__DIR__.'/files/generated.csv'), $csv);
    }
}
