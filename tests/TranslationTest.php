<?php

class TranslationTest extends PHPUnit_Framework_TestCase
{
    public function testReferences()
    {
        //Extract translations
        $translations = Gettext\Extractors\PhpCode::fromFile(__DIR__.'/files/phpcode.php');
        $translation = $translations->find(null, 'text 10 with plural');

        $this->assertInstanceOf('Gettext\\Translation', $translation);

        $references = $translation->getReferences();

        $this->assertCount(1, $references);
        $this->assertTrue($translation->hasReferences());
        $this->assertEquals(__DIR__.'/files/phpcode.php', $references[0][0]);
        $this->assertEquals(19, $references[0][1]);

        $translation->deleteReferences();
        $this->assertCount(0, $translation->getReferences());
    }

    public function testPlurals()
    {
        //Extract translations
        $translations = Gettext\Extractors\PhpCode::fromFile(__DIR__.'/files/phpcode.php');
        $translation = $translations->find(null, 'text 10 with plural');

        $this->assertTrue($translation->hasPlural());
        $this->assertTrue($translation->is('', 'text 10 with plural'));

        $translation = $translations->find(null, 'text 2');

        $this->assertFalse($translation->hasPlural());

        $translation->setPluralTranslation('texts 2');

        $this->assertCount(1, $translation->getPluralTranslation());
        $this->assertEquals('texts 2', $translation->getPluralTranslation(0));
    }

    public function testMerge()
    {
        $one = new Gettext\Translation(null, '1 child');
        $two = new Gettext\Translation(null, '1 child');
        $two->setTranslation('1 fillo');

        $one->mergeWith($two);

        $this->assertEquals('1 child', $one->getOriginal());
        $this->assertEquals('1 fillo', $one->getTranslation());
    }
}
