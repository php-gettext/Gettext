<?php
include_once dirname(__DIR__).'/src/autoloader.php';

class TranslationTest extends PHPUnit_Framework_TestCase
{
    public function testReferences()
    {
        //Extract translations
        $translations = Gettext\Extractors\PhpCode::fromFile(__DIR__.'/files/phpcode.php');
        $translation = $translations->find(null, 'text 10 with plural', 'The plural form');

        $this->assertInstanceOf('Gettext\\Translation', $translation);

        $references = $translation->getReferences();

        $this->assertCount(1, $references);
        $this->assertTrue($translation->hasReferences());
        $this->assertEquals(__DIR__.'/files/phpcode.php', $references[0][0]);
        $this->assertEquals(19, $references[0][1]);

        $translation->wipeReferences();
        $this->assertCount(0, $translation->getReferences());
    }

    public function testPlurals()
    {
        //Extract translations
        $translations = Gettext\Extractors\PhpCode::fromFile(__DIR__.'/files/phpcode.php');
        $translation = $translations->find(null, 'text 10 with plural', 'The plural form');

        $this->assertTrue($translation->hasPlural());
        $this->assertTrue($translation->is('', 'text 10 with plural', 'The plural form'));

        $translation = $translations->find(null, 'text 2');

        $this->assertFalse($translation->hasPlural());

        $translation->setPluralTranslation('texts 2');

        $this->assertCount(1, $translation->getPluralTranslation());
        $this->assertEquals('texts 2', $translation->getPluralTranslation(0));
    }
}